@extends('layouts.app')

@section('style')
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="./css/onsenui.min.css" />
    <link rel="stylesheet" href="./css/onsen-css-components.min.css" />
    <link rel="stylesheet" href="./css/app.css?">

    <script src="./js/onsenui.min.js"></script>
    <script src="./js/d3.min.js"></script>
    <script src="./js/h3-js.js"></script>
    <script src="./js/country.js"></script>
@endsection

@section('content')

</head>

<body>
    <ons-navigator id="myNavigator">
        <ons-page>
            <ons-toolbar>
                <div class="center">Biodiversity Data Visualizer</div>
            </ons-toolbar>

            <ons-tabbar position="auto">
                <ons-tab page="home.html" active icon="ion-ios-home" label="Home"></ons-tab>
                <ons-tab page="spatial.html" icon="ion-ios-globe" label="Spatial"></ons-tab>
                <ons-tab page="species.html" icon="ion-ios-leaf" label="Species"></ons-tab>
                <ons-tab page="time_series.html" icon="ion-ios-clock" label="Time series"></ons-tab>
            </ons-tabbar>
        </ons-page>
    </ons-navigator>

    <template id="home.html">
        <ons-page>
            <div style="text-align:center;">
                <input type="file" id="file" multiple class="button--material" style="font-size: 3em; padding:10px;" placeholder="text" value="">
            </div>
        </ons-page>
    </template>

    <template id="people.html">
        <ons-page>
            <div id="people-container" class="svg-container"></div>
        </ons-page>
    </template>

    <template id="temporal.html">
        <ons-page>
            <div class="svg-container">
                <select id="temporalSelect">
                    <option value="observed_on">Observed on</option>
                    <option value="created_at">Created at</option>
                    <option value="updated_at">Updated at</option>
                </select>
            </div>
            <div id="temporal-container" class="svg-container"></div>
        </ons-page>
    </template>

    <template id="spatial.html">
        <ons-page id="Spatial">
            <div id="map-container" class="svg-container"></div>
        </ons-page>
    </template>

    <template id="species.html">
        <ons-page>
            <div id="breadcrumb" class="svg-container"></div>
            <div id="species-container" class="svg-container"></div>
        </ons-page>
    </template>

    <template id="time_series.html">
        <ons-page>
            <div id="time_series-container" class="svg-container"></div>
        </ons-page>
    </template>

    <ons-modal var="modal">
        <div style="margin: 20px auto; width: 320px;background-color:black;">
            <br>
            <p>Loading. Please Wait</p>
            <ons-progress-circular indeterminate></ons-progress-circular>
        </div>
    </ons-modal>


</body>
    <script>
        var csv_data = [];
var data_set = false;
var coordinates = [];
var speciesHierarchy = {};
const col = 'all';
let svgWidth = 0;
let svgHeight = 0;
const breadcrumbFactor = .75;

if (screen.width < 700) { //for mobile devices
    svgWidth = screen.width - screen.width / 20;
    svgHeight = screen.height - screen.height / 3;
} else {
    svgWidth = 960;
    svgHeight = 650;
}

document.addEventListener('DOMContentLoaded', function() {
    var reader = new FileReader();
    const loadingModal = document.querySelector('ons-modal');

    document.getElementById('file').addEventListener('change', function(e) {
        loadingModal.show();
        reader.addEventListener("load", parseFile, false);
        reader.readAsText(e.target.files[0]);
    });

    function parseFile() {
        const op = [];


        d3.csvParse(reader.result).forEach((d) => {
            op.push({
                "id": +d.id,
                // "user": d.user_login,
                // "observed_on": new Date(d.observed_on),
                // "created_at": new Date(d.created_at),
                // "updated_at": new Date(d.updated_at),
                // "place_guess": d.place_guess,
                "latitude": +d.latitude,
                "longitude": +d.longitude,
                "species": +d.species,
                "total": +d.total,
                // "kingdom": d.taxon_kingdom_name,
                // "phylum": d.taxon_phylum_name,
                // "tclass": d.taxon_class_name,
                // "order": d.taxon_order_name,
                // "family": d.taxon_family_name,
                // "tribe": d.taxon_tribe_name,
                // "genus": d.taxon_genus_name,
                // "species": d.taxon_species_name
            });
        });
        csv_data = op;
        data_set = true;
        // console.log(JSON.stringify(csv_data));

        // speciesHierarchy = buildSpeciesHierarchy(csv_data);
        // console.log(JSON.stringify(speciesHierarchy));
        // speciesHierarchy = newHierarchy();
        // console.log(JSON.stringify(speciesHierarchy));
        // tree();
        // render_species_icicle(speciesHierarchy, svgWidth, svgHeight);
        // renderUsers();
        renderMap();
        // renderTemporal("observed_on");
        /*
        */
        // renderSpecies();

        // render_time_series(csv_data, svgWidth, svgHeight);

        // document.getElementById("temporalSelect").addEventListener('change', function() {
        //     let option = this.value;
        //     renderTemporal(option);
        // });

        loadingModal.hide();
        console.log("Loading Complete");
    }
});

function spatialData() {
    var decimal_precision = 4;

    const data = csv_data;
    data.forEach((d) => {
        var duplicate_flag = false;
        coordinates.forEach((du, i) => {
            if (du.latitude.toFixed(decimal_precision) == parseFloat(d.latitude).toFixed(decimal_precision) && du.longitude.toFixed(decimal_precision) == parseFloat(d.longitude).toFixed(decimal_precision)) {
                duplicate_flag = true;
                coordinates[i].species += d.species;
                coordinates[i].total += d.total;
                coordinates[i].counts++;

            }
        });
        if (!duplicate_flag && !isNaN(parseFloat(d.longitude))) {
            coordinates.push({
                "place_guess": d.place_guess,
                "longitude": parseFloat(d.longitude),
                "latitude": parseFloat(d.latitude),
                "species": d.species,
                "total": d.total,
                "counts": 1
            });
        }
    });
    return (coordinates);
}

function makeGeoJSON_reverse(array) {
    array[0].reverse();
    var geoJSON = {
        "type": "FeatureCollection",
        "features": [{
            "type": "Feature",
            "geometry": {
                "type": "Polygon",
                "coordinates": array
            }
        }]
    }

    return geoJSON;
}

function makeGeoJSON(array) {
    var geoJSON = {
        "type": "FeatureCollection",
        "features": [{
            "type": "Feature",
            "geometry": {
                "type": "Polygon",
                "coordinates": [array]
            }
        }]
    }

    return geoJSON;
}

function hexFeatures(array) {
    const geoJSON = {
        "type": "FeatureCollection",
        "features": [{
            "type": "Feature",
            "geometry": {
                "type": "Polygon",
                "coordinates": [array.reverse()]
            }
        }]
    }
    return geoJSON;

}

function renderMap() {
    //clear svg before loading
    if (!d3.select("#map-container svg").empty()) {
        d3.selectAll("svg").remove();
    }

    const tooltip = d3.select("body").append("div").attr("class", "toolTip");
    var svg = d3.select("#map-container").append("svg").attr("preserveAspectRatio", "xMinYMin meet")
        .attr("width", svgWidth)
        .attr("height", svgHeight)
        .attr("viewBox", "0 0 " + svgWidth + " " + svgHeight)
        .classed("svg-content", true);
    var projection = d3.geoMercator().scale(500).center([78.9, 20.6]);
    var path = d3.geoPath().projection(projection);
    var places = spatialData();
    var h3_zoom = 3;

    // const dark = ['#B08B12', '#BA5F06', '#8C3B00', '#6D191B', '#842854', '#5F7186', '#193556', '#137B80', '#144847', '#254E00'];
    // const mid = ['#E3BA22', '#E58429', '#BD2D28', '#D15A86', '#8E6C8A', '#6B99A1', '#42A5B3', '#0F8C79', '#6BBBA1', '#5C8100'];
    // const light = ['#F2DA57', '#F6B656', '#E25A42', '#DCBDCF', '#B396AD', '#B0CBDB', '#33B6D0', '#7ABFCC', '#C8D7A1', '#A0B700'];
    const light = ["#2979ff", "#2979ff"];

    const palettes = [light];
    const lightGreenFirstPalette = palettes
        .map(d => d.reverse())
        .reduce((a, b) => a.concat(b));
    const color = d3.scaleLinear(lightGreenFirstPalette);

    // console.log("color");
    console.log(places);

    const hexagons = h3.polyfill(country.features[0].geometry.coordinates[0].slice(0, -1).map(d => [d[1], d[0]]), h3_zoom);

    // Get the outline of a set of hexagons, as a GeoJSON-style MultiPolygon
    const coordinates = h3.h3SetToMultiPolygon(hexagons, true);
    const mapBoundary = makeGeoJSON_reverse(coordinates[0]);


    var h3Hex = [];
    const h3Hexes = svg.append("g").selectAll("path");
    places.forEach(p => {
        const h3Address = h3.geoToH3(p.latitude, p.longitude, h3_zoom);
        var matchFlag = false;
        h3Hex.forEach(h => {
            if (h.hexID == h3Address) {
                h.value += p.pointCount;
                matchFlag = true;
            }
        });
        if (matchFlag == false) {
            const h3Boundary = h3.h3ToGeoBoundary(h3Address, true);
            const h3Geo = hexFeatures(h3Boundary);
            h3Hex.push({ "hexID": h3Address, "value_counts":p.counts, "value_species": p.species, "value_total": p.total, "coordinates": h3Geo });
            // h3Hex.push({ "hexID": h3Address, "value": p.latitude +','+ p.longitude, "coordinates": h3Geo });
        }
    });
    console.log(h3Hex);

    h3Hex.forEach(h => {
        h3Hexes.data(h.coordinates.features)
            .enter().append("path")
            .attr("d", path)
            .attr("stroke", "ccc")
            .attr("stroke-width", .25)
            .attr("fill", color(h.value))
            .on("click", (d) => { ons.notification.toast(`${h.value_counts} | ${h.value_species} | ${h.value_total} `, { timeout: 2000, animation: 'lift' }) });
    });




    svg.append("g").selectAll("path").append("g")
        .data(country.features)
        .enter().append("path")
        .attr("d", path)
        .attr("stroke", "red")
        .attr("stroke-width", .1)
        .style("z-index", 10)
        .attr("fill", "none");






    // g.selectAll("circle")
    //     .data(places)
    //     .enter()
    //     .append("circle")
    //     .attr("class", "circles")
    //     .attr("cx", (d) => projection([d.longitude, d.latitude])[0])
    //     .attr("cy", (d) => projection([d.longitude, d.latitude])[1])
    //     .attr("r", (d) => .1)
    //     .on("click", (d) => { ons.notification.toast(` ${d.place_guess}<br>${d.pointCount} Observations`, { timeout: 2000, animation: 'lift' }) });

    // .attr("r", 2 + )
    // .on("mousemove", function(d){
    //     tooltip
    //         .style("left", d3.event.pageX + 20 + "px")
    //         .style("top", d3.event.pageY + 20 + "px")
    //         .style("display", "inline-block")
    //         .html(`${d.place_guess}<br>${d.pointCount} Observations`);
    //     })
    // .on("mouseout", function(d){ tooltip.style("display", "none");});


    var zoom = d3.zoom()
        .scaleExtent([1, 30])
        .on('zoom', function() {
            svg.selectAll('circle')
                .attr('transform', d3.event.transform),
                svg.selectAll('path')
                .attr('transform', d3.event.transform);
        });

    svg.call(zoom);
}

function data_prepare() {
    // const data = data;
    let op = [];
    let newFlag = true;
    csv_data.forEach(d => {
        if (col == 'all') {
            newFlag = true;
            op.forEach(da => {
                if (da.user === d.user) {
                    da.observations++;
                    newFlag = false;
                }
            });
            if (newFlag) {
                op.push({
                    "user": d.user,
                    "observations": 1,
                });
            }
        }
    });
    return op;
}

function renderUsers() {
    const data = data_prepare();
    const margin = { top: 10, right: 30, bottom: 30, left: 40 };
    const width = svgWidth - margin.left - margin.right;
    const height = svgHeight - margin.top - margin.bottom;
    const selected_col = col;


    const tooltip = d3.select("body").append("div").attr("class", "toolTip");

    const tooltip_date = (user_data) => {
        return user_data.user + "-" + user_data.observations;
    }

    const xValue = (d) => d.user;
    const xAxisLabel = "Users";
    const yValue = (d) => d.observations;
    const yAxisLabel = "Observations";
    const title = `${yAxisLabel} vs. ${xAxisLabel}`;
    const circleRadius = 15;

    const xScale = d3.scaleBand()
        .domain(data.map(value => value.user))
        .range([0, width])
        .padding(0.1);

    const yScale = d3.scaleLinear()
        .domain([0, d3.max(data.map(value => value.observations))])
        .range([height, 0])
        .nice();

    if (!d3.select("#people-container svg").empty()) {
        d3.selectAll("svg").remove();
    }

    var svg = d3.select("#people-container").append("svg")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
        .append("g")
        .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    const xAxis = d3.axisBottom(xScale)
        .tickSize(margin.left + margin.right - width)
        .tickPadding(15);

    const yAxis = d3.axisLeft(yScale)
        .tickSize(-innerWidth)
        .tickPadding(10);

    const chart = svg.selectAll("rect").data(data);
    if (data_set) {
        chart.enter().append("rect")
            .attr("class", "bar")
            .attr("transform", (d) => `translate(0, ${yScale(d.observations)} )`)
            .attr("x", (d) => xScale(xValue(d)))
            .attr("width", xScale.bandwidth())
            .attr("height", (d) => height - yScale(yValue(d)))
            .on("mousemove", (d) => {
                tooltip
                    .style("left", d3.event.pageX + 20 + "px")
                    .style("top", d3.event.pageY + 20 + "px")
                    .style("display", "inline-block")
                    .html(tooltip_date(d));
            })
            .on("mouseout", function(d) { tooltip.style("display", "none"); });

        // add the x Axis
        svg.append("g")
            .attr("transform", "translate(0," + height + ")")
            .call(d3.axisBottom(xScale));

        // // add the y Axis
        svg.append("g")
            .call(d3.axisLeft(yScale));

        svg.append('text')
            .attr('class', 'title')
            .attr('y', -10)
            .text(title)

    }
}

function process_temporal_data(col) {
    // const data = csv_data;
    // let col = "observed_on";
    // console.log(col);
    // document.getElementById('temporalSelect').addEventListener('change', () => {
    //     col = document.getElementById('temporalSelect').find(":selected").text();
    //     alert(col);
    // })
    let op = [];
    csv_data.forEach(d => {
        if (d[col].getFullYear() > 189) {
            op.push({
                "id": d["id"],
                "month": d[col]
            });

        }
    });

    return op;
}

function renderTemporal(col) {

    const data = process_temporal_data(col);
    const margin = { top: 30, right: 30, bottom: 30, left: 40 };
    const width = svgWidth - margin.left - margin.right;
    const height = svgHeight - margin.top - margin.bottom;
    const yChartTitle = -10;
    const xChartTitle = width / 2 - width / 11;

    // const selected_col = "observed_on";
    // console.log(JSON.stringify(data));

    const tooltip = d3.select("body").append("div").attr("class", "toolTip");
    const tooltip_date = (date) => {
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return months[date.getMonth()] + ", " + date.getFullYear();
    }

    const xValue = (d) => d.date;
    const xAxisLabel = "Time";
    // const yValue = (d) => d.id;
    // const yAxisLabel = "Observations";
    const yAxisLabel = (col == 'observed_on') ? "Observations" : (col == 'created_at') ? "Created at" : (col == 'updated_at') ? "Updated at" : 0;
    const title = `${yAxisLabel} vs. ${xAxisLabel}`;

    const xScale = d3.scaleTime()
        .domain(d3.extent(data, xValue))
        .range([0, width])
        .nice();

    const yScale = d3.scaleLinear()
        .range([height, 0])
        .nice();

    const histogram = d3.histogram()
        .value(xValue)
        .domain(xScale.domain())
        .thresholds(xScale.ticks(d3.timeDay));

    if (!d3.select("#temporal-container svg").empty()) {
        d3.selectAll("svg").remove();
    }

    var svg = d3.select("#temporal-container").append("svg")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
        .append("g")
        .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    var bins = histogram(data);

    yScale.domain([0, d3.max(bins, function(d) { return d.length; })]);

    const chart = svg.selectAll("rect").data(bins);

    if (data_set) {
        chart.enter().append("rect")
            .attr("class", "bar")
            .attr("x", 1)
            .attr("transform", (d) => `translate(${xScale(d.x0)}, ${yScale(d.length)} )`)
            .attr("width", (d) => xScale(d.x1) - xScale(d.x0))
            .attr("height", (d) => height - yScale(d.length))
            .on("mousemove", (d) => {
                tooltip
                    .style("left", d3.event.pageX + 20 + "px")
                    .style("top", d3.event.pageY + 20 + "px")
                    .style("display", "inline-block")
                    .html(tooltip_date(d.x1) + "<br>" + d.length + " observations");
            })
            .on("mouseout", function(d) { tooltip.style("display", "none"); });

        // add the x Axis
        svg.append("g")
            .attr("transform", "translate(0," + height + ")")
            .call(d3.axisBottom(xScale));

        // add the y Axis
        svg.append("g")
            .call(d3.axisLeft(yScale));

        svg.append('text')
            .attr('class', 'title')
            .attr('y', yChartTitle)
            .attr('x', xChartTitle)
            .text(title)

    }
}

function newHierarchy() {
    var all_data = csv_data.map(d => { return d.kingdom + "--" + d.phylum + "--" + d.tclass + "--" + d.order + "--" + d.family + "--" + d.genus + "--" + d.species });
    var unique_taxa = [];
    all_data.forEach(d => {
        if (unique_taxa.indexOf(d) == -1) {
            unique_taxa.push(d);
        }
    })
    unique_taxa = unique_taxa.map(d => d.split("--"));
    // console.log(unique_taxa);
    /*
    var root = d3.nest()
                    .key((d) => d.kingdom)
                    .key((d) => d.phylum)
                    .key((d) => d.tclass)
                    .key((d) => d.order)
                    .key((d) => d.family)
                    // .key((d) => d.tribe)
                    .key((d) => d.genus)
                    .key((d) => d.species)
                        .entries(csv_data);
    console.log(root);
    var renamed = {
        "name": "Life",
        "children": root[0].values.map(function(kingdom) {
            return {
                "name": kingdom.key,
                "children": kingdom.values.map(function(phylum) {
                    return {
                        "name": phylum.key,
                        "children": phylum.values.map(function(tclass) {
                            return {
                                "name": tclass.key,
                                "children": tclass.values.map(function(order) {
                                    return {
                                        "name": order.key,
                                        "children": order.values.map(function(family) {
                                            return {
                                                "name": family.key,
                                                "children": family.values.map(function(genus) {
                                                    return {
                                                        "name": genus.key,
                                                        "children": genus.values.map(function(species) {
                                                            return {
                                                                "name": species.key,
                                                                "children": species.values
                                                            };

                                                        })
                                                    };
                                                })
                                            };
                                        }) //end of map(function(country){
                                    };
                                })
                            };

                        }) //end of map(function(country){
                    };

                }) //end of map(function(region){
            };

        }) //end of map(function(major){
    }; //end of var declaration
    // console.log(renamed);
    return renamed;
    */

}

function buildSpeciesHierarchy(all_data) {
    // var all_data = csv_data.map(d => { return d.kingdom + "--" + d.phylum + "--" + d.tclass + "--" + d.order + "--" + d.family + "--" + d.genus + "--" + d.species });
    var all_data = csv_data.map(d => { return d.order + "--" + d.family + "--" + d.genus + "--" + d.species});
    var unique_taxa = [];
    all_data.forEach(d => {
        if (unique_taxa.indexOf(d) == -1) {
            unique_taxa.push(d);
        }
    })
    unique_taxa = unique_taxa.map(d => d.split("--"));


    // const csv = all_data.map(d => { return [d.kingdom, d.phylum, d.class, d.order, d.family, d.genus, d.species] });

    const root = { name: "Life", children: [] };

    // console.log(all_data);
    // console.log(unique_taxa);
    unique_taxa.forEach((parts, i) => {
        let currentNode = root;
        let nodes_with_values = [];

        parts.forEach((nodeName, j) => {
            const children = currentNode["children"];
            let childNode = null;

            if (nodeName != undefined && nodeName != "") {
                nodes_with_values[j] = nodeName;
                if (j + 1 < parts.length) {
                    // Not yet at the end of the sequence; move down the tree.
                    let foundChild = false;
                    for (let k = 0; k < children.length; k++) {
                        if (children[k]["name"] == nodeName) {
                            childNode = children[k];
                            foundChild = true;
                            break;
                        }
                    }
                    // If we don't already have a child node for this branch, create it.

                    if (!foundChild) {
                        childNode = { name: nodeName, children: [] };
                        children.push(childNode);
                    }
                    currentNode = childNode;
                } else {
                    // Reached the end of the sequence; create a leaf node.
                    childNode = { name: nodeName, value: 1 };
                    // if(nodeName.length > 2)
                    children.push(childNode);
                }
            }
        });

    });

    // console.log(JSON.stringify(root));
    return root;
}

function renderSpecies() {
    var data = speciesHierarchy;
    var format = d3.format(",d");
    var width = svgWidth;
    var height = svgHeight;
    var radius = width / 12;
    var maxRadius = Math.min(width, height) / 2 - 5;
    var partition = data => {
        const root = d3.hierarchy(data)
            .sum(d => 1)
            .sort((a, b) => b.value - a.value);
        return d3.partition()
            .size([2 * Math.PI, root.height + 1])(root);
    }

    const x = d3.scaleLinear()
        .range([0, 2 * Math.PI])
        .clamp(true);
    const y = d3.scaleSqrt().range([maxRadius * 0.1, maxRadius]);

    // sunlight style guide network colors
    // https://github.com/amycesal/dataviz-style-guide/blob/master/Sunlight-StyleGuide-DataViz.pdf
    const dark = ['#B08B12', '#BA5F06', '#8C3B00', '#6D191B', '#842854', '#5F7186', '#193556', '#137B80', '#144847', '#254E00'];
    const mid = ['#E3BA22', '#E58429', '#BD2D28', '#D15A86', '#8E6C8A', '#6B99A1', '#42A5B3', '#0F8C79', '#6BBBA1', '#5C8100'];
    const light = ['#F2DA57', '#F6B656', '#E25A42', '#DCBDCF', '#B396AD', '#B0CBDB', '#33B6D0', '#7ABFCC', '#C8D7A1', '#A0B700'];
    const palettes = [light, mid, dark];
    const lightGreenFirstPalette = palettes
        .map(d => d.reverse())
        .reduce((a, b) => a.concat(b));
    const color = d3.scaleOrdinal(lightGreenFirstPalette);

    var arc = d3.arc()
        .startAngle(d => d.x0)
        .endAngle(d => d.x1)
        .padAngle(d => Math.min((d.x1 - d.x0) / 2, 0.005))
        .padRadius(radius * 1.5)
        .innerRadius(d => d.y0 * radius)
        .outerRadius(d => Math.max(d.y0 * radius, d.y1 * radius))
    const root = partition(data);

    root.each(d => d.current = d);
    const svg = d3.select("#species-container").append("svg")
        .attr("width", width)
        .attr("height", height)
        .style("font", "10px sans-serif");
    const g = svg.append("g")
        .attr("transform", `translate(${width / 2},${height / 2})`);
    const path = g.append("g")
        .selectAll("path")
        .data(root.descendants().slice(1))
        .enter().append("path")
        .attr("fill", d => { while (d.depth > 2) d = d.parent; return color(d.data.name); })
        .style("fill", d => {
            if (d.current ? (d.children ? 0 : (d.data.common_name != 'undefined' && d.data.common_name != '')) : 0) {
                return d3.color("red");
            } else {
                while (d.depth > 3) {
                    d = d.parent;
                }
                return color(d.data.name);
            }
        })
        .attr("fill-opacity", d => arcVisible(d.current) ? (d.children ? 0.6 : 0.4) : 0)
        .attr("d", d => arc(d.current));

    path.filter(d => d.current)
        .style("cursor", "pointer")
        .on("mouseover", function(d) { d3.select(this).classed("selected", true) })
        .on("mouseout", function(d) { d3.select(this).classed("selected", false) })
        .on("click", clicked);
    path.append("title")
        .text(d => `${d.ancestors().map(d => d.data.name).reverse().join("/")}\n${format(d.value)}`);
    const label = g.append("g")
        .attr("pointer-events", "all")
        .attr("text-anchor", "middle")
        .style("user-select", "none")
        .selectAll("text")
        .data(root.descendants().slice(1))
        .enter().append("text")
        .attr("dy", "0.35em")
        .attr("fill-opacity", d => +labelVisible(d.current))
        .attr("transform", d => labelTransform(d.current))
        .text(d => d.data.name);
    const parent = g.append("circle")
        .datum(root)
        .attr("r", radius)
        .attr("fill", "none")
        .attr("pointer-events", "all")
        .on("click", clicked);

    function populate_breadcrumbs(p) {
        if (p.parent != null)
            return (populate_breadcrumbs(p.parent) + " > " + p.data.name);
        else if (p.data != null)
            return (p.data.name);
        else
            alert("problem");
    }

    function update_json(root) {
        var new_array = [];
        $.each(dataSet, function(key, value) {
            if ((value[3] == root) || (value[4] == root) || (value[5] == root) || (value[6] == root) || (value[7] == root))
                new_array.push(value);
        });
        return (new_array);
    }

    function clicked(p) {
        var selected_taxon = p.data.name;
        // document.getElementById("breadcrumbs").innerHTML(populate_breadcrumbs(p));

        parent.datum(p.parent || root);
        root.each(d => d.target = {
            x0: Math.max(0, Math.min(1, (d.x0 - p.x0) / (p.x1 - p.x0))) * 2 * Math.PI,
            x1: Math.max(0, Math.min(1, (d.x1 - p.x0) / (p.x1 - p.x0))) * 2 * Math.PI,
            y0: Math.max(0, d.y0 - p.depth),
            y1: Math.max(0, d.y1 - p.depth)
        });
        const t = g.transition().duration(250);
        path.transition(t)
            .tween("data", d => {
                const i = d3.interpolate(d.current, d.target);
                return t => d.current = i(t);
            })
            .filter(function(d) {
                return +this.getAttribute("fill-opacity") || arcVisible(d.target);
            })
            .attr("fill-opacity", d => arcVisible(d.target) ? (d.children ? 0.6 : 0.4) : 0)
            .attrTween("d", d => () => arc(d.current));
        label.filter(function(d) {
                return +this.getAttribute("fill-opacity") || labelVisible(d.target);
            }).transition(t)
            .attr("fill-opacity", d => +labelVisible(d.target))
            .attrTween("transform", d => () => labelTransform(d.current));
    }

    function arcVisible(d) {
        return d.y1 <= 3 && d.y0 >= 1 && d.x1 > d.x0;
    }

    function labelVisible(d) {
        return d.y1 <= 3 && d.y0 >= 1 && (d.y1 - d.y0) * (d.x1 - d.x0) > 0.03;
    }

    function labelTransform(d) {
        const x = (d.x0 + d.x1) / 2 * 180 / Math.PI;
        const y = (d.y0 + d.y1) / 2 * radius;
        return `rotate(${x - 90}) translate(${y},0) rotate(${x < 180 ? 0 : 180})`;
    }

    d3.select("body").append("svg")
        .attr("width", width)
        .attr("height", height)
        .append("g")
        .attr("transform", "translate(" + width / 2 + "," + (height / 2) + ")");
}

function tree() {
    const width = 960;
    const height = 500;
    var dx = 10;
    var dy = width / 6;
    const margin = ({ top: 10, right: 120, bottom: 10, left: 40 });
    var data = speciesHierarchy;
    var diagonal = d3.linkHorizontal().x(d => d.y).y(d => d.x);
    const tree = d3.tree().nodeSize([dx, dy]);
    const root = d3.hierarchy(data);
    root.x0 = dy / 2;
    root.y0 = 0;
    root.descendants().forEach((d, i) => {
        d.id = i;
        d._children = d.children;
        if (d.depth && d.data.name.length !== 7) d.children = null;
    });
    console.log(root, data);

    const svg = d3.select("#species-container").append("svg")
        .attr("width", width)
        .attr("height", height)
        .style("font", "10px sans-serif");

    const gLink = svg.append("g")
        .attr("fill", "none")
        .attr("stroke", "#555")
        .attr("stroke-opacity", 0.4)
        .attr("stroke-width", 1.5);

    const gNode = svg.append("g")
        .attr("cursor", "pointer")
        .attr("pointer-events", "all");

    function update(source) {
        const duration = d3.event && d3.event.altKey ? 2500 : 250;
        const nodes = root.descendants().reverse();
        const links = root.links();

        // Compute the new tree layout.
        tree(root);

        let left = root;
        let right = root;
        root.eachBefore(node => {
            if (node.x < left.x) left = node;
            if (node.x > right.x) right = node;
        });

        const height = right.x - left.x + margin.top + margin.bottom;

        const transition = svg.transition()
            .duration(duration)
            .attr("viewBox", [-margin.left, left.x - margin.top, width, height])
            .tween("resize", window.ResizeObserver ? null : () => () => svg.dispatch("toggle"));

        // Update the nodes…
        const node = gNode.selectAll("g")
            .data(nodes, d => d.id);

        // Enter any new nodes at the parent's previous position.
        const nodeEnter = node.enter().append("g")
            .attr("transform", d => `translate(${source.y0},${source.x0})`)
            .attr("fill-opacity", 0)
            .attr("stroke-opacity", 0)
            .on("click", (d) => {
                console.log(d);
                d.children = d.children ? null : d._children;
                update(d);
            });

        nodeEnter.append("circle")
            .attr("r", 2.5)
            .attr("fill", d => d._children ? "#555" : "#999")
            .attr("stroke-width", 10);

        nodeEnter.append("text")
            .attr("dy", "0.31em")
            .attr("x", d => d._children ? -6 : 6)
            .attr("text-anchor", d => d._children ? "end" : "start")
            .text(d => d.data.name)
            .clone(true).lower()
            .attr("stroke-linejoin", "round")
            .attr("stroke-width", 3)
            .attr("stroke", "white");

        // Transition nodes to their new position.
        const nodeUpdate = node.merge(nodeEnter).transition(transition)
            .attr("transform", d => `translate(${d.y},${d.x})`)
            .attr("fill-opacity", 1)
            .attr("stroke-opacity", 1);

        // Transition exiting nodes to the parent's new position.
        const nodeExit = node.exit().transition(transition).remove()
            .attr("transform", d => `translate(${source.y},${source.x})`)
            .attr("fill-opacity", 0)
            .attr("stroke-opacity", 0);

        // Update the links…
        const link = gLink.selectAll("path")
            .data(links, d => d.target.id);

        // Enter any new links at the parent's previous position.
        const linkEnter = link.enter().append("path")
            .attr("d", d => {
                const o = { x: source.x0, y: source.y0 };
                return diagonal({ source: o, target: o });
            });

        // Transition links to their new position.
        link.merge(linkEnter).transition(transition)
            .attr("d", diagonal);

        // Transition exiting nodes to the parent's new position.
        link.exit().transition(transition).remove()
            .attr("d", d => {
                const o = { x: source.x, y: source.y };
                return diagonal({ source: o, target: o });
            });

        // Stash the old positions for transition.
        root.eachBefore(d => {
            d.x0 = d.x;
            d.y0 = d.y;
        });
    }

    update(root);

    return svg.node();

}

function render_species_icicle(species_data, width, height) {
    const data = species_data;

    const partition = data => {
        const root = d3.hierarchy(data)
            .sum(d => d.value)
            .sort((a, b) => b.height - a.height || b.value - a.value);
        return d3.partition()
            .size([height, (root.height + 1) * width / 3])
            (root);
    }

    const format = d3.format(",d");

    // Breadcrumb dimensions: width, height, spacing, width of tip/tail.
    let b = {
        w: 150,
        h: 30,
        s: 3,
        t: 10
    };
    // const color = d3.scaleOrdinal(d3.quantize(d3.interpolateRainbow, data.children.length + 1));
    const color = d3.scaleOrdinal().domain(data).range(d3.schemeSet2);

    const root = partition(data);
    let focus = root;
    let totalSize = 0;

    //add breadcrumb
    initializeBreadcrumbTrail();
    let percentage = 100;
    let percentageString = percentage + "%";

    d3.select("#percentage")
        .text(percentageString);

    d3.select("#explanation")
        .style("visibility", "");

    let sequenceArray = root.ancestors().reverse();
    //sequenceArray.shift(); // remove root node from the array
    updateBreadcrumbs(sequenceArray, percentageString);

    // console.log(root);
    const svg = d3
        .select("#species-container")
        .append('svg')
        // .attr("viewBox", [0, 0, width, height])
        .attr("width", width)
        .attr("height", height)
        .style("font", "10px sans-serif");

    const cell = svg
        .selectAll("g")
        .data(root.descendants())
        .join("g")
        .attr("transform", d => `translate(${d.y0},${d.x0})`);

    const rect = cell.append("rect")
        .attr("width", d => d.y1 - d.y0 - 1)
        .attr("height", d => {
            return rectHeight(d);
        })
        .attr("fill-opacity", 0.6)
        .attr("fill", d => {
            if (!d.depth) return "#ccc";
            while (d.depth > 1) d = d.parent;
            // console.log(d);
            return color(d.data.name);
        })
        .style("cursor", "pointer")
        .on("click", clicked);
    // console.log(focus);

    //get total size from rect
    totalSize = rect.node().__data__.value;

    const text = cell.append("text")
        .style("user-select", "none")
        .attr("pointer-events", "none")
        .attr("x", 4)
        .attr("y", 13)
        .attr("fill-opacity", d => +labelVisible(d));

    text.append("tspan")
        .text(d => d.data.name);

    const tspan = text.append("tspan")
        .attr("fill-opacity", d => labelVisible(d) * 0.7)
        .text(d => ` ${format(d.value)}`);

    cell.append("title")
        .text(d => `${d.ancestors().map(d => d.data.name).reverse().join("/")}\n${format(d.value)}`);

    function clicked(p, event) {
        focus = focus === p ? p = p.parent : p;

        root.each(d => d.target = {
            x0: (d.x0 - p.x0) / (p.x1 - p.x0) * height,
            x1: (d.x1 - p.x0) / (p.x1 - p.x0) * height,
            y0: d.y0 - p.y0,
            y1: d.y1 - p.y0
        });

        const t = cell.transition().duration(750)
            .attr("transform", d => `translate(${d.target.y0},${d.target.x0})`);

        rect.transition(t).attr("height", d => rectHeight(d.target));
        text.transition(t).attr("fill-opacity", d => +labelVisible(d.target));
        tspan.transition(t).attr("fill-opacity", d => labelVisible(d.target) * 0.7);

        // code to update the BreadcrumbTrail();
        let percentage = (100 * p.value / totalSize).toPrecision(3);
        let percentageString = percentage + "%";
        if (percentage < 0.1) {
            percentageString = "< 0.1%";
        }

        d3.select("#percentage")
            .text(percentageString);

        d3.select("#explanation")
            .style("visibility", "");

        let sequenceArray = p.ancestors().reverse();
        //sequenceArray.shift(); // remove root node from the array
        updateBreadcrumbs(sequenceArray, percentageString);

    }

    function rectHeight(d) {
        return d.x1 - d.x0 - Math.min(1, (d.x1 - d.x0) / 2);
    }

    function labelVisible(d) {
        return d.y1 <= width && d.y0 >= 0 && d.x1 - d.x0 > 16;
    }

    function initializeBreadcrumbTrail() {
        // Add the svg area.
        let trail = d3.select("#breadcrumb").append("svg")
            .attr("width", width)
            .attr("height", 50)
            .attr("id", "trail");
        // Add the label at the end, for the percentage.
        trail.append("text")
            .attr("id", "endlabel")
            .style("fill", "#000");

        // Make the breadcrumb trail visible, if it's hidden.
        d3.select("#trail")
            .style("visibility", "");
    }

    // Generate a string that describes the points of a breadcrumb polygon.
    function breadcrumbPoints(d, i) {
        let points = [];
        points.push("0,0");
        points.push(b.w * breadcrumbFactor + ",0");
        points.push(b.w * breadcrumbFactor + b.t + "," + (b.h / 2));
        points.push(b.w * breadcrumbFactor + "," + b.h);
        points.push("0," + b.h);
        if (i > 0) { // Leftmost breadcrumb; don't include 6th vertex.
            points.push(b.t + "," + (b.h / 2));
        }
        return points.join(" ");
    }

    // Update the breadcrumb trail to show the current sequence and percentage.
    function updateBreadcrumbs(nodeArray, percentageString) {

        // Data join; key function combines name and depth (= position in sequence).
        let trail = d3.select("#trail")
            .selectAll("g")
            .data(nodeArray, function(d) { return d.data.key + d.depth; });

        // Remove exiting nodes.
        trail.exit().remove();

        // Add breadcrumb and label for entering nodes.
        let entering = trail.enter().append("g");

        entering.append("polygon")
            .attr("points", breadcrumbPoints)
            .style("fill", function(d) { return color((d.children ? d : d.parent).data.key); });

        entering.append("text")
            .attr("x", (b.w * breadcrumbFactor + b.t) / 2)
            .attr("y", b.h / 2)
            .attr("dy", "0.35em")
            .attr("text-anchor", "middle")
            .attr("font-size", ".85em")
            .text(function(d) {
                return d.data.name;
            });

        // Merge enter and update selections; set position for all nodes.
        entering.merge(trail).attr("transform", function(d, i) {
            return "translate(" + i * (b.w * breadcrumbFactor + b.s) + ", 0)";
        });

        // Now move and update the percentage at the end.
        d3.select("#trail").select("#endlabel")
            .attr("x", (nodeArray.length + 0.5) * (b.w + b.s) * breadcrumbFactor)
            .attr("y", b.h / 2)
            .attr("dy", "0.35em")
            .attr("text-anchor", "middle")
            .text(percentageString);
    }
}

function generate_time_series_data(csv_data) {
    const col = "observed_on";
    let raw_data = [];
    csv_data.forEach(d => {
        if (d[col].getFullYear() > 189) {
            raw_data.push({
                "id": d["id"],
                "month": d[col]
            });

        }
    });

    let data = [];

    raw_data.forEach((rows, i) => {
        let counter = 0;

        let top_index = raw_data.length;

        while (top_index > 0) {
            top_index--;
            if (+rows.month == +raw_data[top_index].month) {
                counter++;
            }
        }
        var obj = { count: counter, month: raw_data[i].month };
        data.push(obj);
    });

    // format month as a date
    data.forEach(function(d) {
        d.month = new Date(d.month);
    });

    // sort data by month
    data.sort(function(x, y) {
        return d3.ascending(x.month, y.month);
    });

    return data;
}

function render_time_series(csv_data, svgWidth, svgHeight) {
    var svg = d3.select("#time_series-container").append("svg").attr('width', 960).attr('height', 500).attr('class', 'metric-chart'),
        margin = { top: 50, right: 40, bottom: 130, left: 105 },
        margin2 = { top: 430, right: margin.right, bottom: 30, left: margin.left },
        width = svgWidth - margin.left - margin.right,
        height = svgHeight - margin.top - margin.bottom,
        height2 = svgHeight - margin2.top - margin2.bottom;

    const yAxisLabel = "No. of observations";
    const xAxisLabel = "Time";

    const data = generate_time_series_data(csv_data);

    //===================================================================================================
    // the date range of available data:
    var dataXrange = d3.extent(data, function(d) { return d.month; });
    var dataYrange = [0, d3.max(data, function(d) { return d.count; })];

    var parseDate = d3.timeParse("%b %Y");
    var formatTime = d3.timeFormat("%B %d, %Y");

    var x = d3.scaleTime().domain(dataXrange).range([0, width]),
        y = d3.scaleLinear().domain(dataYrange).range([height, 0]),
        x2 = d3.scaleTime().domain(x.domain()).range([0, width]),
        y2 = d3.scaleLinear().domain(y.domain()).range([height2, 0]);

    // x.domain(d3.extent(data, function (d) { return d.month; }));
    // y.domain([0, d3.max(data, function (d) { return d.count; })]);
    // x2.domain(x.domain());
    // y2.domain(y.domain());

    // console.log(formatTime(dataXrange[0]) + " - " + formatTime(dataXrange[1]));
    // console.log(parseDate(dataXrange[0]), parseDate(dataXrange[1]));

    var xAxis = d3.axisBottom(x).tickSize(-height),
        yAxis = d3.axisLeft(y).tickSize(-width).tickPadding(10),
        xAxis2 = d3.axisBottom(x2);

    var brush = d3.brushX()
        .extent([
            [0, 0],
            [width, height2]
        ])
        .on("brush end", brushed);

    var zoom = d3.zoom()
        .scaleExtent([1, Infinity])
        .translateExtent([
            [0, 0],
            [width, height]
        ])
        .extent([
            [0, 0],
            [width, height]
        ])
        .on("zoom", zoomed);

    /* === focus chart === */
    var area = d3.area()
        // .curve(d3.curveMonotoneX)        //curve area for focus chart
        .x(function(d) { return x(d.month); })
        .y0(height)
        .y1(function(d) { return y(d.count); });

    var line = d3.line()
        .x(function(d) { return x(d.month); })
        .y(function(d) { return y(d.count); });

    /* === context chart === */
    var area2 = d3.area()
        // .curve(d3.curveMonotoneX)        //curve area for smaller chart
        .x(function(d) { return x2(d.month); })
        .y0(height2)
        .y1(function(d) { return y2(d.count); });

    var line2 = d3.line()
        .x(function(d) { return x2(d.month); })
        .y(function(d) { return y2(d.count); });

    const textColor = "#626160";

    /* ============ create DOM elements ================ */
    svg.append("defs").append("clipPath")
        .attr("id", "clip")
        .append("rect")
        .attr("width", width)
        .attr("height", height);

    const displayDates = svg.append('g')
        .attr('transform', `translate(${margin.left},${margin.top / 5})`);

    displayDates.append('text')
        .text("Showing data from: ")
        .style('text-anchor', 'start')
        .attr('fill', textColor)
        .attr("transform", "translate(" + 0 + "," + 10 + ")");

    displayDates.append("text")
        .attr("id", "displayDates")
        .text(formatTime(dataXrange[0]) + " - " + formatTime(dataXrange[1]))
        .style("text-anchor", "start")
        .attr("transform", "translate(" + (margin.left + 45) + "," + 10 + ")");

    var focus = svg.append("g")
        .attr("class", "focus")
        .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    focus.append('text')
        .attr('class', 'axis-label')
        .attr('y', -height / 10)
        .attr('x', -height / 2)
        .attr('fill', 'black')
        .attr('text-anchor', 'middle')
        .attr('transform', `rotate(-90)`)
        .text(yAxisLabel);

    focus.append('text')
        .attr('class', 'axis-label')
        .attr('y', height + 45)
        .attr('x', width / 2)
        .attr('fill', 'black')
        .text(xAxisLabel);


    var context = svg.append("g")
        .attr("class", "context")
        .attr("transform", "translate(" + margin2.left + "," + margin2.top + ")");

    /* === focus chart === */
    const xAxisG = focus.append("g")
        .attr("class", "axis axis--y")
        .call(yAxis);

    focus.append("path")
        .datum(data)
        .attr("class", "area")
        .attr("d", area);

    const yAxisG = focus.append("g")
        .attr("class", "axis axis--x")
        .attr("transform", "translate(0," + height + ")")
        .call(xAxis);

    focus.append("path")
        .datum(data)
        .attr("class", "line")
        .attr("d", line);

    /* === context chart === */

    context.append("path")
        .datum(data)
        .attr("class", "area")
        .attr("d", area2);

    // context.append("path")
    //     .datum(data)
    //     .attr("class", "line")
    //     .attr("d", line2);

    context.append("g")
        .attr("class", "axis axis--x")
        .attr("transform", "translate(0," + height2 + ")")
        .call(xAxis2);

    /* === brush (part of context chart)  === */
    context.append("g")
        .attr("class", "brush")
        .call(brush)
        .call(brush.move, x.range());

    svg.append("rect")
        .attr("class", "zoom")
        .attr("width", width)
        .attr("height", height)
        .attr("transform", "translate(" + margin.left + "," + margin.top + ")")
        .call(zoom);

    remove_black_outlines(); //remove outlines initially when page loads
    context.selectAll(".axis--x .domain").remove(); //remove dark line from context chart (x-axis)

    function remove_black_outlines() {
        xAxisG.selectAll("path").remove();
        yAxisG.selectAll("path").remove();
    }

    function brushed() {
        if (d3.event.sourceEvent && d3.event.sourceEvent.type === "zoom") return; // ignore brush-by-zoom
        var s = d3.event.selection || x2.range();
        x.domain(s.map(x2.invert, x2));
        focus.select(".area").attr("d", area);
        focus.select(".line").attr("d", line);
        focus.select(".axis--x").call(xAxis);
        svg.select(".zoom").call(zoom.transform, d3.zoomIdentity
            .scale(width / (s[1] - s[0]))
            .translate(-s[0], 0));
        // updateDisplayDates();
        remove_black_outlines(); //remove outlines when user interacts with context chart
    }

    function zoomed() {
        setYdomain();
        if (d3.event.sourceEvent && d3.event.sourceEvent.type === "brush") return; // ignore zoom-by-brush
        var t = d3.event.transform;
        x.domain(t.rescaleX(x2).domain());
        focus.select(".area").attr("d", area);
        focus.select(".line").attr("d", line);
        focus.select(".axis--x").call(xAxis);
        context.select(".brush").call(brush.move, x.range().map(t.invertX, t));
        // console.log(x.domain());
        updateDisplayDates();
        remove_black_outlines(); //remove outlines when focus chart is zoomed
    }

    function setYdomain() {
        // this function dynamically changes the y-axis to fit the data in focus

        // get the min and max date in focus
        var xleft = new Date(x.domain()[0]);
        var xright = new Date(x.domain()[1]);

        // a function that finds the nearest point to the right of a point
        var bisectDate = d3.bisector(function(d) { return d.month; }).right;

        // get the y value of the line at the left edge of view port:
        var iL = bisectDate(data, xleft);

        if (data[iL] !== undefined && data[iL - 1] !== undefined) {

            var left_dateBefore = data[iL - 1].month,
                left_dateAfter = data[iL].month;

            var intfun = d3.interpolateNumber(data[iL - 1].count, data[iL].count);
            var yleft = intfun((xleft - left_dateBefore) / (left_dateAfter - left_dateBefore));
        } else {
            var yleft = 0;
        }

        // get the x value of the line at the right edge of view port:
        var iR = bisectDate(data, xright);

        if (data[iR] !== undefined && data[iR - 1] !== undefined) {

            var right_dateBefore = data[iR - 1].month,
                right_dateAfter = data[iR].month;

            var intfun = d3.interpolateNumber(data[iR - 1].count, data[iR].count);
            var yright = intfun((xright - right_dateBefore) / (right_dateAfter - right_dateBefore));
        } else {
            var yright = 0;
        }

        // get the y values of all the actual data points that are in view
        var dataSubset = data.filter(function(d) { return d.month >= xleft && d.month <= xright; });
        var countSubset = [];
        dataSubset.map(function(d) { countSubset.push(d.count); });

        // add the edge values of the line to the array of counts in view, get the max y;
        countSubset.push(yleft);
        countSubset.push(yright);
        var ymax_new = d3.max(countSubset);

        if (ymax_new == 0) {
            ymax_new = dataYrange[1];
        }

        // reset and redraw the yaxis
        y.domain([0, ymax_new * 1.05]);
        focus.select(".axis.axis--y").call(yAxis);

    };

    function type(d) {
        d.month = parseDate(d.month);
        d.count = +d.count;
        return d;
    }

    function updateDisplayDates() {

        var b = x.domain();
        // update the text that shows the range of displayed dates

        var localBrushDateStart = formatTime(b[0]),
            localBrushDateEnd = formatTime(b[1]);

        // Update start and end dates in upper right-hand corner
        d3.select("#displayDates")
            .text(localBrushDateStart == localBrushDateEnd ? localBrushDateStart : localBrushDateStart + " - " + localBrushDateEnd);
    };
}
document.addEventListener('prechange', function(event) {
    const label = event.tabItem.getAttribute('label');
    // const selector = document.querySelector('ons-toolbar .center');
    // if (label == 'People') {
    // } else if (label == 'Spatial') {
    // } else if (label == 'Temporal') {
    // } else if (label == 'Species') {
    // }
});

    </script>
@endsection
