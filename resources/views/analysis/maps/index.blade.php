<!DOCTYPE html>
<html>
<head>
    <title></title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('js/d3.min.js') }}"></script>
    <script src="{{ asset('js/h3-js.js') }}"></script>
    <script src="{{ asset('js/country.js') }}"></script>
    <style>
        .hexagon{
            stroke-width:.3;
            stroke: #5a5;
            opacity:.7;
        }
        .hexagon:hover{
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="table-secondary m-1 p-2 row">
        <div id="map-container" class="svg-container bg-light col"></div>
        <div class="col table-info" style="max-height: 850px;overflow-y: scroll;">
            <h1 class="w-100">Map Data</h1>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Common Name</th>
                        <th>Scientific Name</th>
                        <th>Family</th>
                        <th>Individuals</th>
                        <th>Source</th>
                    </tr>
                </thead>
                <tbody id="map-data"></tbody>
            </table>
        </div>
    </div>
<script>
    const svgWidth = 800;
    const svgHeight = 850;
    const zoom = 4;
    const data = {
     "butterfly_counts": @json($forms),
     "inat": @json($inats),
     "ibp": @json($ibps)
    }
    var h3Hex = [];

    renderMap();

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
        .classed("svg-content", true);
    var projection = d3.geoMercator().scale(1400).center([85.5, 29.5]);
    var path = d3.geoPath().projection(projection);

    svg.append("g")
        .classed("map-boundary", true)
        .selectAll("path").append("g")
        .data(country.features)
        .enter().append("path")
        .attr("d", path)
        .attr("stroke", "#999")
        .attr("stroke-width", .5)
        .style("z-index", 10)
        .attr("fill", "none");

    renderHex(svg, path);

    var zoom = d3.zoom()
        .scaleExtent([0, 40])
        .on('zoom', function() {
            svg.selectAll('circle')
                .attr('transform', d3.event.transform),
            svg.selectAll('text')
                .attr('transform', d3.event.transform),
            svg.selectAll('path')
                .attr('transform', d3.event.transform);
        });

    svg.call(zoom);

}
function display_data(id){
    table = ""
    var cleaned_rows = []
    h3Hex[id].rows.forEach(r => {
        if(cleaned_rows[r.scientific_name] == undefined){
            cleaned_rows[r.scientific_name] = {
                "scientific_name": r.scientific_name,
                "common_name": r.common_name,
                "family": r.family,
                "individuals": r.no_of_individuals_cleaned,
                "source": r.source
            }
        } else {
            cleaned_rows[r.scientific_name].individuals += parseInt(r.no_of_individuals_cleaned)
            if(!cleaned_rows[r.scientific_name].source.includes(r.source))
                cleaned_rows[r.scientific_name].source += ", " + r.source
        }
    })
    var cleaned = Object.values(cleaned_rows);
    cleaned.sort(function(a,b) {
        return b.individuals - a.individuals
    });

    Object.values(cleaned).forEach(p => {
        table += "<tr><td>" + p.common_name + "</td><td>" + p.scientific_name + "</td><td>" + p.family + "</td><td class='text-center'>" + p.individuals + "</td><td>"+p.source+"</td></tr>";
    })


    document.getElementById('map-data').innerHTML = table;
}
function renderHex(svg, path)
{
    const label_size = zoom*1.2;
    const hexColor = {
        1: "#D7F4D2",
        2: "#BFEEB7",
        3: "#A7E997",
        4: "#77DD66"
    };

    // d3.select(".hex-content").remove();

    const h3Hexes = svg.append("g").classed("hex-content", true).selectAll("path")

    Object.keys(data).forEach(source => {
        data[source].forEach(p => {
            const h3Address = h3.geoToH3(p.latitude, p.longitude, zoom)


            var matchFlag = false;
            h3Hex.forEach((h,i) => {
                if (h.hexID == h3Address) {
                    matchFlag = true;
                    matchID = i;
                }
            });
            if (matchFlag == false) {
                var h3Boundary = h3.h3ToGeoBoundary(h3Address, true);
                var h3Geo = hexFeatures(h3Boundary);
                if(source == "butterfly_counts"){
                    row = []
                    p.rows.forEach(r => {
                        row.push({
                            "scientific_name": r.scientific_name,
                            "common_name": r.common_name,
                            "family": r.family,
                            "no_of_individuals_cleaned": r.no_of_individuals_cleaned,
                            "source": source
                        })
                    })
                    h3Hex.push({ "hexID": h3Address, "counts":1 , "species_count": p.species, "total": parseInt(p.total), "coordinates": h3Geo, "rows": row });

                } else {
                        sci_name_cleaned = p.scientific_name.split(' ').slice(0,2).join(' ')
                    row = {
                        "scientific_name": sci_name_cleaned,
                        "common_name": p.common_name,
                        "family": p.family,
                        "no_of_individuals_cleaned": 1,
                        "source": source
                    }
                    h3Hex.push({ "hexID": h3Address, "counts":1 , "species_count": 1, "total": 1, "coordinates": h3Geo, "rows": [row] });
                }
            }
            else {
                if(source == "butterfly_counts"){
                    row = []
                    p.rows.forEach(r => {
                        row.push({
                            "scientific_name": r.scientific_name,
                            "common_name": r.common_name,
                            "family": r.family,
                            "no_of_individuals_cleaned": r.no_of_individuals_cleaned,
                            "source": source
                        })
                    })
                    h3Hex[matchID].counts += 1;
                    h3Hex[matchID].species_count += p.species;
                    h3Hex[matchID].total += parseInt(p.total);
                    h3Hex[matchID].rows = h3Hex[matchID].rows.concat(row)
                } else {
                    sci_name_cleaned = p.scientific_name.split(' ').slice(0,2).join(' ')
                    row = {
                        "scientific_name": sci_name_cleaned,
                        "common_name": p.common_name,
                        "family": p.family,
                        "no_of_individuals_cleaned": 1,
                        "source": source
                    }
                    h3Hex[matchID].counts += 1;
                    h3Hex[matchID].species_count += 1;
                    h3Hex[matchID].total += 1;
                    h3Hex[matchID].rows = h3Hex[matchID].rows.concat(row)


                }

            }
        });
    });

    const display_field = "species_count";
    // const display_field = "counts";

    h3Hex.forEach((h,i) => {
        const x = h3Hexes.data(h.coordinates.features)
        const y = x.enter().append("g")
        const digits = h[display_field].toString().length

        y.append("text")
            .attr("x", function(h) { return path.centroid(h)[0]; })
            .attr("y", function(h) { return path.centroid(h)[1]; })
            .attr("dx", -1*(digits*1.5))
            .attr("alignment-baseline", "central")
            .style("font-size", label_size)
            .style("font-weight", "bold")
            .style("fill", "#000")
            .text(h[display_field]);

        y.append("path")
            .attr("d", path)
            .attr("class", "hexagon")
            .attr("fill", hexColor[digits])
            .attr('onclick',"display_data('"+i+"')");


    });
}


    </script>
</body>
</html>
