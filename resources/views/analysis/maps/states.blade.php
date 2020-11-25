<!DOCTYPE html>
<html>
<head>
    <title></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/5.9.2/d3.min.js" integrity="sha512-U3BExhsSSzrscvztemnZwpCsZfbKnEFXOIezIrAi3y7sjiALdlRQsknCyp/KBKnaJZxjQXvLBT4UPkyq06BnJA==" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/h3-js"></script>
    <script src="{{ asset('js/country_r.js') }}"></script>
    <script src="{{ asset('js/point_in_state.js') }}"></script>
    <style>
        .hexagon{
            stroke-width:.3;
            stroke: #5a5;
            opacity:.5;
        }
        .map-boundary path:hover{
            cursor: pointer;
            fill: lightyellow;
            stroke:red;
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
            <div id="map_controls" class="border border-success table-success">
                <div class="row m-3 text-center">
                    <div class="col">
                        <h4>Filter by Species</h4>
                        <input type="search" class="form-control mb-3" id="selectedSpecies" list="selectSpecies" onchange="selectSpeciesFunction()">
                        <datalist id="selectSpecies">
                        </datalist>

                        {{-- <select class="custom-select custom-select-lg mb-3" id="selectSpecies" "></select> --}}
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
            <div class="my-2 border border-danger px-3 bg-light text-center" style="max-height: 25vh;overflow-y: scroll;">
                <h3>Places</h3>
                <div id="places" class="row"></div>
            </div>
            <div class="my-2 border border-danger px-3 bg-light text-center" style="max-height: 25vh;overflow-y: scroll;">
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
    var data = point_in_state
    const svgWidth = window.innerWidth / 2
    const svgHeight = window.innerHeight - 30


    var filterSpecies = "ALL"
    var svg = 0
    var path = 0
    var zoom = 4
    var species_list = []

    renderMap()

    var select = document.getElementById("selectSpecies")
    data.forEach(p => {
        if(!species_list.includes(p.scientific_name))
            species_list.push(p.scientific_name)
    });

    var options = species_list.sort()
    options.unshift("ALL")

    for(var i = 0; i < options.length; i++) {
        var opt = options[i]
        var el = document.createElement("option")
        el.textContent = opt
        el.value = opt
        select.appendChild(el)
    }

    function selectSpeciesFunction(){
        filterSpecies = document.getElementById("selectedSpecies").value
        console.log(filterSpecies)
        d3.selectAll("svg").remove()
        h3Hex = []
        renderMap()
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
    function populateStateData(data, source, state){

        rows = []
        if(source == "butterfly_counts"){
            data.rows_cleaned.forEach(r => {
                if(filterSpecies == "ALL" || filterSpecies == r.scientific_name){
                    rows.push({
                        "scientific_name": r.scientific_name,
                        "common_name": r.common_name,
                        "individuals": parseInt(r.individuals),
                        "names": data.name,
                        "place": data.place,
                        "source": source
                    })
                }
            })
        } else {
            if(filterSpecies == "ALL" || filterSpecies == data.scientific_name){
                rows = [{
                    "scientific_name": data.scientific_name,
                    "common_name": data.common_name,
                    "individuals": 1,
                    "names": data.name,
                    "place": data.place,
                    "source": source
                }]
            }

        }
        rows.forEach(r=>{
            stateData[state].push(r)

        })


    }
    function renderMap() {
        //clear svg before loading
        if (!d3.select("#map-container svg").empty()) {
            d3.selectAll("svg").remove()
        }

        svg = d3.select("#map-container").append("svg").attr("preserveAspectRatio", "xMinYMin meet")
            .attr("width", svgWidth)
            .attr("height", svgHeight)
            .classed("svg-content", true)
        var projection = d3.geoMercator().scale(1400).center([85.5, 29.5])
        path = d3.geoPath().projection(projection)

        base = svg.append("g")
            .classed("map-boundary", true)
            .selectAll("path").append("g")

        country.features.forEach(state=> {

            base.append("g")
                .data([state])
                .enter().append("path")
                .attr("d", path)
                .attr("stroke", "#999")
                .attr("stroke-width", .5)
                .style("z-index", 10)
                .attr("fill", "#fff")
                .on("click", (d) => console.log(d.properties.ST_NM))
        })
        // renderHex(svg, path)

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
            return value["names"] + "-" + value["source"]
        }).filter(function(v, i, self){
            return i == self.indexOf(v)
        });
        observers = "";
        unique_observers.forEach(o => {
            if(o.includes("ibp"))
                observers += '<div class="col text-nowrap border border-warning table-warning text-center rounded m-1">'+o.replace("-ibp", "")+'</div>'
            else if (o.includes("inat"))
                observers += '<div class="col text-nowrap border border-success table-success text-center rounded m-1">'+o.replace("-inat", "")+'</div>'
            else
                observers += '<div class="col text-nowrap border border-info table-info text-center rounded m-1">'+o.replace("-butterfly_counts", "")+'</div>'
        })
        var unique_places = h3Hex[id].rows.map(function(value,index) {
            return value["place"].replace(", India", "").trim() + "-" + value.source
        }).filter(function(v, i, self){
            return i == self.indexOf(v);
        })
        places = "";
        unique_places.forEach(p => {
            if(p.includes("ibp"))
                places += '<div class="col text-nowrap border border-warning table-warning text-center rounded m-1">'+p.replace("-ibp", "")+'</div>'
            else if (p.includes("inat"))
                places += '<div class="col text-nowrap border border-success table-success text-center rounded m-1">'+p.replace("-inat", "")+'</div>'
            else
                places += '<div class="col text-nowrap border border-info table-info text-center rounded m-1">'+p.replace("-butterfly_counts", "")+'</div>'
        })
        document.getElementById('data-species').innerHTML = unique_species.length
        document.getElementById('data-individuals').innerHTML = total_individuals
        document.getElementById('data-observers').innerHTML = unique_observers.length

        document.getElementById('observers').innerHTML = observers
        document.getElementById('places').innerHTML = places


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

</script>
</body>
</html>
