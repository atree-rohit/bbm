<!DOCTYPE html>
<html>
<head>
    <title></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/5.9.2/d3.min.js" integrity="sha512-U3BExhsSSzrscvztemnZwpCsZfbKnEFXOIezIrAi3y7sjiALdlRQsknCyp/KBKnaJZxjQXvLBT4UPkyq06BnJA==" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/h3-js"></script>
    <script src="{{ asset('js/country.js') }}"></script>
    <style>
        .hexagon{
            stroke-width:.3;
            stroke: #5a5;
            opacity:.5;
        }
        .hexagon:hover{
            cursor: pointer;
        }
        /*.hex_text{
            font-size: 6px;
        }*/
    </style>
</head>
<body>
    <div class="table-secondary m-1 p-2 row">

        <div id="map-container" class="svg-container bg-light col"></div>

        <div class="col table-info" style="max-height: 95vh;overflow-y: scroll;">
            <div id="map_controls container-fluid d-flex justify-content-around" class="border border-success">
                <div class="row">
                    <div class="col">
                        <div class="slidecontainer">
                            <h4>Hexagon Size: <span id="zoom_value"></span></h4>
                            <input type="range" min="1" max="7" value="4" class="slider" id="zoom_slider">
                        </div>
                    </div>
                    <div class="col">

                        Filter by Species<select class="custom-select custom-select-lg mb-3" id="selectSpecies"></select>

                    </div>
                    
                </div>
                
            </div>
            <div class="d-flex justify-content-around my-2 text-light text-center">
                <div class="card bg-info">
                    <div class="card-body">
                        <h1 class="card-title" id="data-species">-</h1>
                        <h3 class="card-subtitle mb-2">Species</h3>
                    </div>
                </div>
                <div class="card bg-info">
                    <div class="card-body">
                        <h1 class="card-title" id="data-individuals">-</h1>
                        <h3 class="card-subtitle mb-2">Individuals</h3>
                    </div>
                </div>
                <div class="card bg-info">
                    <div class="card-body">
                        <h1 class="card-title" id="data-observers">-</h1>
                        <h3 class="card-subtitle mb-2">Observers</h3>
                    </div>
                </div>
            </div>
            <div class="my-2 border border-danger px-3 bg-light text-center">
                <h3>Observers</h3>
                <div id="observers" class="row"></div>
            </div>
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Common Name</th>
                        <th>Scientific Name</th>
                        <th>Individuals</th>
                        <th>Source</th>
                    </tr>
                </thead>
                <tbody id="map-data"></tbody>
            </table>
        </div>
    </div>
<script>
    
    const svgWidth = window.innerWidth / 2
    const svgHeight = window.innerHeight - 30

    var h3Hex = []
    const data = {
         "butterfly_counts": @json($forms),
         "inat": @json($inats),
         "ibp": @json($ibps)
    }
    var zoom = 4
    var svg = 0
    var path = 0
    var species_list = []

    renderMap()

    var slider = document.getElementById("zoom_slider")
    var output = document.getElementById("zoom_value")
    output.innerHTML = slider.value

    slider.oninput = function() {
        output.innerHTML = this.value
        d3.selectAll("svg").remove()
        zoom = this.value
        h3Hex = []
        renderMap()
    }

    var select = document.getElementById("selectSpecies")
    Object.keys(data).forEach(source => {
        data[source].forEach(p => {
            if(!species_list.includes(p.scientific_name))
                species_list.push(p.scientific_name)
        });
    });
    var options = species_list.sort();

    for(var i = 0; i < options.length; i++) {
        var opt = options[i]
        var el = document.createElement("option")
        el.textContent = opt
        el.value = opt
        select.appendChild(el)
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
            d3.selectAll("svg").remove()
        }

        const tooltip = d3.select("body").append("div").attr("class", "toolTip");
        svg = d3.select("#map-container").append("svg").attr("preserveAspectRatio", "xMinYMin meet")
            .attr("width", svgWidth)
            .attr("height", svgHeight)
            .classed("svg-content", true);
        var projection = d3.geoMercator().scale(1400).center([85.5, 29.5]);
        path = d3.geoPath().projection(projection);

        svg.append("g")
            .classed("map-boundary", true)
            .selectAll("path").append("g")
            .data(country.features)
            .enter().append("path")
            .attr("d", path)
            .attr("stroke", "#999")
            .attr("stroke-width", .5)
            .style("z-index", 10)
            .attr("fill", "none")

        renderHex(svg, path)

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

        var unique_species = h3Hex[id].rows.map(function(value,index) { 
            return value["scientific_name"]; 
        }).filter(function(v, i, self){
            return i == self.indexOf(v);
        })    
        var total_individuals = 0
        h3Hex[id].rows.forEach(r => {
            total_individuals += parseInt(r.individuals)
        })
        var unique_observers = h3Hex[id].rows.map(function(value,index) { 
            return value["names"] + " (" + value["source"] +")"; 
        }).filter(function(v, i, self){
            return i == self.indexOf(v);
        }); 
        observers = "";
        unique_observers.forEach(o => {
            if(o.includes("ibp"))
                observers += '<div class="col text-nowrap border border-warning table-warning text-center rounded m-1">'+o+'</div>'
            else if (o.includes("inat"))
                observers += '<div class="col text-nowrap border border-success table-success text-center rounded m-1">'+o+'</div>'
            else
                observers += '<div class="col text-nowrap border border-info table-info text-center rounded m-1">'+o+'</div>'
        })
        document.getElementById('data-species').innerHTML = unique_species.length
        document.getElementById('data-individuals').innerHTML = total_individuals
        document.getElementById('data-observers').innerHTML = unique_observers.length
        
        document.getElementById('observers').innerHTML = observers
        

        h3Hex[id].rows.forEach(r => {
            if(cleaned_rows[r.scientific_name] == undefined){
                cleaned_rows[r.scientific_name] = {
                    "scientific_name": r.scientific_name,
                    "common_name": r.common_name,
                    "individuals": parseInt(r.individuals),
                    "names": r.names,
                    "source": r.source
                }
            } else {
                cleaned_rows[r.scientific_name].individuals += parseInt(r.individuals)
                if(!cleaned_rows[r.scientific_name].source.includes(r.source))
                    cleaned_rows[r.scientific_name].source += ", " + r.source
                if(!cleaned_rows[r.scientific_name].names.includes(r.names))
                    cleaned_rows[r.scientific_name].names += ", " + r.names
            }
        })
        var cleaned = Object.values(cleaned_rows).sort(function(a,b) {
            return b.individuals - a.individuals
        });

        Object.values(cleaned).forEach(p => {
            table += "<tr><td>" + p.common_name + "</td><td>" + p.scientific_name + "</td><td class='text-center'>" + p.individuals +  "</td><td>"+p.source+"</td></tr>";
        })


        document.getElementById('map-data').innerHTML = table;
    }
    function renderHex(svg, path)
    {
        const label_size = {
            1: 50,
            2: 25,
            3: 12,
            4: 6
        }
        var myColor = d3.scaleLinear().domain([1,50]).range(["red", "green"])

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
                        p.rows_cleaned.forEach(r => {
                            row.push({
                                "scientific_name": r.scientific_name,
                                "common_name": r.common_name,
                                "individuals": parseInt(r.individuals),
                                "names": p.name,
                                "source": source
                            })
                        })
                        h3Hex.push({ "hexID": h3Address, "coordinates": h3Geo, "rows": row });

                    } else {
                        row = {
                            "scientific_name": p.scientific_name,
                            "common_name": p.common_name,
                            "individuals": 1,
                            "names": p.name,
                            "source": source
                        }
                        h3Hex.push({ "hexID": h3Address, "coordinates": h3Geo, "rows": [row] });
                    }
                }
                else {
                    if(source == "butterfly_counts"){
                        row = []
                        p.rows_cleaned.forEach(r => {
                            row.push({
                                "scientific_name": r.scientific_name,
                                "common_name": r.common_name,
                                "individuals": parseInt(r.individuals),
                                "names": p.name,
                                "source": source
                            })
                        })
                        h3Hex[matchID].rows = h3Hex[matchID].rows.concat(row)
                    } else {
                        row = {
                            "scientific_name": p.scientific_name,
                            "common_name": p.common_name,
                            "individuals": 1,
                            "names": p.name,
                            "source": source
                        }
                        h3Hex[matchID].rows = h3Hex[matchID].rows.concat(row)
                    }

                }
            });
        });
        

        h3Hex.forEach((h,i) => {
            uniqueSpecies = h.rows.map(function(value,index) { 
                return value["scientific_name"]; 
            }).filter(function(v, i, self){
                return i == self.indexOf(v);
            }); 
            h3Hex[i].species = uniqueSpecies.length
        });


        h3Hex.forEach((h,i) => {
            const x = h3Hexes.data(h.coordinates.features)
            const y = x.enter().append("g")
            const digits = h.species.toString().length

            if(zoom < 5){
                y.append("text")
                    .classed("hex_text", true)
                    .attr("x", function(h) { return path.centroid(h)[0]; })
                    .attr("y", function(h) { return path.centroid(h)[1]; })
                    .attr("dx", -1*(digits*1.5) * (5-zoom))
                    .attr("dy", 2.5)
                    .attr("font-size", label_size[zoom])
                    .text(h.species)                
            }

            y.append("path")
                .attr("d", path)
                .attr("class", "hexagon")
                .attr("fill", myColor(h.species) )
                .attr('onclick',"display_data('"+i+"')")
        });
    }

</script>
</body>
</html>
