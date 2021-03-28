const { Client } = require("@elastic/elasticsearch");
const client = new Client({ node: "http://localhost:9200" });
const fs = require("fs");

const csv = require("fast-csv");

let iterator = 0;

function createBDD(file) {
  let data = {};
  const stream = fs
    .createReadStream("CSV/" + file + ".csv")
    .pipe(csv.parse({ headers: true }))
    .on("data", function (dataFile) {
      for (let [key, value] of Object.entries(dataFile)) {
        if (!isNaN(value)){
          if (parseInt(value) === parseFloat(value)){
            value = parseInt(value);
          } else {
            value = parseFloat(value);
          }
        }
        data[key] = value;
      };
      // const dataJson = JSON.stringify(dataOk);
      // Let's start by indexing some data
      client.index({
        index: file,
        body: {
          data,
        },
      });
    })
    .on("end", function () {
      iterator++;
      console.log("Read finish");
      if (iterator === 6) {
        console.log("Script finished");
        return process.exit(22);
      }
    });
}

function init() {
  createBDD("steam");
  createBDD("steam_description_data");
  createBDD("steam_media_data");
  createBDD("steam_requirements_data");
  createBDD("steam_support_info");
  createBDD("steamspy_tag_data");
}

init();
