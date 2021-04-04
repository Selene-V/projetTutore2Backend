const { Client } = require("@elastic/elasticsearch");
const client = new Client({ node: "http://localhost:9200" });
const fs = require("fs");

const csv = require("fast-csv");
function createBDD(file) {
  return new Promise((resolve) => {
    console.log("Creating index", file);
    let inserts = [];
    const stream = fs
      .createReadStream("CSV/" + file + ".csv")
      .pipe(csv.parse({ headers: true }))
      .on("data", function (dataFile) {
        let data = {};
        let negativeRatings = 0;
        let positiveRatings = 0;

        for (let [key, value] of Object.entries(dataFile)) {
          if (keyToChangeValue.includes(key)) {
            value = value.split(";");
          }
          // Comme le prix n'est pas utile ici, nous ne l'ajoutons pas Ã  la db
          if (key === "price") {
            continue;
          }
          if (!isNaN(value)) {
            value = parseInt(value);
          }
          if (file === "steam") {
            if (key === "negative_ratings") {
              negativeRatings = value;
            }
            if (key === "positive_ratings") {
              positiveRatings = value;
            }
          }

          data[key] = value;
        }
        if (file === "steam") {
          data["positive_review_percentage"] =
            Math.round(
              (positiveRatings / (positiveRatings + negativeRatings)) *
                100 *
                100
            ) / 100;
        }
        // Let's start by indexing some data
        inserts.push(
          client.index({
            index: file,
            body: {
              data,
            },
          })
        );
      })
      .on("end", async function () {
        console.log("Read finish", file);
        console.log("Waiting for inserts to finish...");
        await Promise.all(inserts);
        console.log("Inserts finished", file);
        resolve();
      });
  });
}

const indexes = [
  "steam",
  "steam_description_data",
  "steam_media_data",
  "steam_requirements_data",
  "steam_support_info",
  "steamspy_tag_data",
];

const keyToChangeValue = [
  "developer",
  "publisher",
  "platforms",
  "categories",
  "genres",
  "steamspy_tags",
];

async function purgeDB() {
  console.log("Purging current indexes if they exist");
  let promises = indexes.map((item) => client.indices.delete({ index: item }));
  await Promise.allSettled(promises);
  console.log("Purging finished");
}

async function createDB() {
  console.log("Creating new indexes with current dataset");
  for (let i = 0; i < indexes.length; i++) {
    await createBDD(indexes[i]);
  }
  console.log("All indexes created");
}

async function init() {
  await purgeDB();
  await createDB();
  console.log("Script finished successfully");
  process.exit(0);
}

init();
