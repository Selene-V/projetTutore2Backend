const { Client } = require("@elastic/elasticsearch");
const client = new Client({ node: "http://localhost:9200" });
const fs = require("fs");

const csv = require("fast-csv");
function createBDD(file) {
    return new Promise((resolve) => {
        console.log("Creating index", file);
        try {
            let inserts = [];
            const stream = fs
                .createReadStream("CSV/" + file + ".csv")
                .pipe(csv.parse({headers: true}))
                .on("data", function (dataFile) {
                    let data = {};
                    let negativeRatings = 0;
                    let positiveRatings = 0;

                    for (let [key, value] of Object.entries(dataFile)) {

                        if (keyToChangeValue.includes(key)) {
                            value = value.split(';');
                        }

                        if (!isNaN(value)) {
                            value = parseInt(value);
                        }

                        if (file === 'steam') {
                            if (key === "negative_ratings") {
                                negativeRatings = value;
                            }
                            if (key === "positive_ratings") {
                                positiveRatings = value;
                            }
                        }

                        data[key] = value;
                    }
                    if (file === 'steam') {
                        data['positive_review_percentage'] = Math.round((positiveRatings / (positiveRatings + negativeRatings) * 100) * 100) / 100;
                    }
                    // process.exit(0);
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
        }catch (error){
            console.log('--------ERREUR--------');
            console.log(error);
        }
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
    "steamspy_tags"
]

async function purgeDB() {
    console.log("Purging current indexes if they exist");
    let promises = indexes.map((item) => client.indices.delete({ index: item }));
    await Promise.allSettled(promises);
    console.log("Purging finished");
}

async function createDB() {
    console.log("Creating new indexes with current dataset");
    for (let i = 0; i < indexes.length; i++) {
        if (indexes[i]==='steam'){
            console.log('---ON EST DANS Lindex STEAM---');
            await createDBSteam();
        }
        await createBDD(indexes[i]);

    }
    console.log("All indexes created");
}
async function createDBSteam(){
    console.log("Modify mappings index steam");
    const mappings = {
        achievements: {type: "long"},
        appid: {type: "long"},
        average_playtime: {type: "long"},
        categories: {type: "text",
            fields: {
                keyword: {
                    type: "keyword",
                    ignore_above: 256
                }
            }
        },
        developer: {type: "text",
            analyser: "autocomplete",
            fields: {
                keyword: {
                    type: "keyword",
                    ignore_above: 256
                }
            }
        },
        english: {type: "long"},
        genres: {type: "text",
            fields: {
                keyword: {
                    type: "keyword",
                    ignore_above: 256
                }
            }
        },
        median_playtime: {type: "long"},
        name: {type: "text",
            analyser: "autocomplete",
            fields: {
                keyword: {
                    type: "keyword",
                    ignore_above: 256
                }
            }
        },
        negative_ratings: {type: "long"},
        owners: {type: "text",
            fields: {
                keyword: {
                    type: "keyword",
                    ignore_above: 256
                }
            }
        },
        platforms: {type: "text",
            fields: {
                keyword: {
                    type: "keyword",
                    ignore_above: 256
                }
            }
        },
        positive_ratings: {type: "long"},
        positive_review_percentage: {type: "float"},
        publisher: {type: "text",
            analyser: "autocomplete",
            fields: {
                keyword: {
                    type: "keyword",
                    ignore_above: 256
                }
            }
        },
        release_date: {type: "date"},
        required_age: {type: "long"},
        price: {type: "float"},
        steamspy_tags: {type: "text",
            analyser: "autocomplete",
            fields: {
                keyword: {
                    type: "keyword",
                    ignore_above: 256
                }
            }
        },

    };

    const settings = {
        analysis: {
            filter: {
                autocomplete_filter: {
                    type: "edge_ngram",
                    min_gram: 1,
                    max_gram: 20
                }
            },
            analyser: {
                autocomplete: {
                    type: "custom",
                    tokenizer: "standard",
                    filter: [
                        "lowercase",
                        "autocomplete_filter"
                    ]
                }
            }
        }
    };

    console.log('On fait le putmapping');
    // inserts.push(
    //     client.indices.putMapping( {
    client.indices.create({
            index: "steam",
            body: {
                settings: {
                    settings
                },
                mappings:{
                    properties: {
                        mappings
                    }
                }
            }
        },
        function(err, resp, status) {
            if (err) {
                console.log('-------ERREUR------');
                console.log(err);
                console.log('--------RESP------');
                console.log(resp);

            }
        });
    console.log('FIn du putMapping');
}

async function init() {
    await purgeDB();
    await createDB();
    console.log("Script finished successfully");
    process.exit(0);
}

init();
