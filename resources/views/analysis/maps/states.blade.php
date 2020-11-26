<!DOCTYPE html>
<html>
<head>
    <title></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/5.9.2/d3.min.js" integrity="sha512-U3BExhsSSzrscvztemnZwpCsZfbKnEFXOIezIrAi3y7sjiALdlRQsknCyp/KBKnaJZxjQXvLBT4UPkyq06BnJA==" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3-legend/2.25.6/d3-legend.min.js"></script>
    <script src="{{ asset('js/country_r.js') }}"></script>
    <script src="{{ asset('js/point_in_state.js') }}"></script>
    <style>
        .selected_polygon{
            fill: #00f;
            stroke:#ff0;
        }
        .poly_text{
            z-index:100;
        }
        .map-boundary path:hover{
            cursor: pointer;
            fill: lightyellow;
            stroke:red;
        }
        .map-boundary path.selected_polygon:hover{
            fill: #f55;
        }
        #map-legend{
            border:2px solid red !important;
        }
        /*width*/
        ::-webkit-scrollbar {
          width: 10px;
        }

        /* Track */
        ::-webkit-scrollbar-track {
          background: #f1f1f1;
        }

        /* Handle */
        ::-webkit-scrollbar-thumb {
          background: #888;
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
          background: #555;
        }
    </style>
</head>
<body>
    <div class="table-secondary m-1 p-2 row">

        <div id="map-container" class="svg-container bg-light col"></div>

        <div class="col table-info" style="max-height: 95vh;overflow-y: none;">
            <div id="map_controls" class="border border-success table-success text-center">
                <span class="h3 p-4">Labels</span>
                <div class="btn-group mb-2" id="btn_gp">
                </div>
                <div id="places" class="h1 d-block"></div>
            </div>
            <div class="d-flex justify-content-around my-2 text-light text-center">
                <div class="card bg-info">
                    <div class="card-body">
                        <h1 class="card-title" id="data-species">-</h1>
                        <h3 class="card-subtitle mb-2">Unique Taxa</h3>
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
                <h3>Observers</h3>
                <div id="observers" class="row"></div>
            </div>
            <div style="max-height: 40vh;overflow-y: scroll;">
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
    </div>
<script>
    var data = point_in_state
    const svgWidth = window.innerWidth / 2
    const svgHeight = window.innerHeight - 30
    var svg = 0
    var path = 0
    var zoom = 4
    var species_list = []
    var state_data = []
    var state_species = []

    var largest_total = 0
    var label_type = "unique_taxa"

    const label_button = {
        "state_name": "State Name",
        "unique_taxa": "Unique Taxa",
        "individuals": "Individuals",
        "observers": "Observers"
    }
    Object.keys(label_button).forEach(k=>{
        var element = document.createElement("button");
        element.type = "button"
        element.value = k
        element.id = k + "_btn"
        if(k == "state")
            element.setAttribute("class", "btn  btn-success")
        else
        element.setAttribute("class", "btn  btn-outline-danger")
        element.setAttribute("onclick", "toggle_button('"+k+"_btn')")

        element.innerHTML = label_button[k]
        document.getElementById("btn_gp").appendChild(element)
    })

    function toggle_button(e){
        label_type = e.replace("_btn", "")
        document.getElementById(e).classList.toggle("btn-outline-danger")
        document.getElementById(e).classList.toggle("btn-success")
        calculateLabels(label_type)
    renderMap()
    display_data("None")
    }

    data.forEach(s => {

        if(state_data[s[7]] == undefined)
            state_data[s[7]] = []
        state_data[s[7]].push(s)
    })

    function calculateLabels(label_type){
        if(label_type == "unique_taxa"){
            Object.keys(state_data).forEach(s => {
                var uniques = state_data[s].map(function(value,index) {
                        return value[4];
                    }).filter(function(v, i, self){
                        return i == self.indexOf(v);
                    })
                if(uniques.length > largest_total)
                    largest_total = uniques.length
                state_species[s] = uniques.length
            })
        }
        if(label_type == "individuals"){
            total
            Object.keys(state_data).forEach(s => {
                state_data[s].forEach(r => {
                        return value[4];
                    }).filter(function(v, i, self){
                        return i == self.indexOf(v);
                    })
                if(uniques.length > largest_total)
                    largest_total = uniques.length
                state_species[s] = uniques.length
            })
        }
    }

    calculateLabels(label_type)
    renderMap()
    display_data("None")


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
        const colors = d3.scaleLinear().domain([0, 1, 500]).range(["#999", "#f77", "#7f7"])
        var legend = d3.legendColor().scale(colors).shapeWidth(55).labelFormat(d3.format(".0f")).orient('horizontal').cells(6)



        base = svg.append("g")
            .classed("map-boundary", true)

        base_text = base.selectAll("text").append("g")
        base = base.selectAll("path").append("g")

        country.features.forEach(state=> {
            if(state_species[state.properties.ST_NM] == undefined)
                state_species[state.properties.ST_NM] = 0
            base.append("g")
                .data([state])
                .enter().append("path")
                .attr("d", path)
                .attr("stroke", "#333")
                .attr("id", (d) => d.properties.ST_NM)
                .attr("stroke-width", .5)
                .attr("fill", (d) => colors(state_species[d.properties.ST_NM]))
                .on("click", (d) => display_data(d.properties.ST_NM))
        })
        country.features.forEach(state=> {
            x = base_text.append("g")
                .data([state])
                .enter().append("text")
                    .classed("poly_text", true)
                    .attr("x", (d) => path.centroid(d)[0])
                    .attr("y", (d) => path.centroid(d)[1])
                    .attr("text-anchor", "middle")
                    .on("click", (d) => display_data(d.properties.ST_NM))
            if(label_type == "state_name")
                x.text((d) => d.properties.ST_NM)
            else if(label_type == "unique_taxa")
                x.text((d) => state_species[d.properties.ST_NM])
        })
        svg.append("g")
            .attr("id", "map-legend")
            .append("g")
            .attr("transform", "translate(450, 10)")
            .call(legend);

        var zoom = d3.zoom()
            .scaleExtent([0, 40])
            .on('zoom', function() {
                svg.selectAll('.poly_text')
                .attr('transform', d3.event.transform),
                svg.selectAll('path')
                .attr('transform', d3.event.transform);
            });
        svg.call(zoom);
    }

    function display_data(state){
        table = ""
        var cleaned_rows = []

        polygons = document.getElementsByClassName("map-boundary")[0].children

        if(state != "None"){
            current_classes = document.getElementById(state).classList
            if(current_classes.length > 0)
                state = "None"
        }
        Object.values(polygons).forEach(p => {
            if(p.id === state)
                document.getElementById(p.id).classList.add("selected_polygon")
            else if(document.getElementById(p.id) != null)
                document.getElementById(p.id).classList.remove("selected_polygon")
        })
        if(state_data[state] == undefined){
            unique_species = ""
            total_individuals = "0"
            observers = "-"
            unique_observers = ""
            cleaned = ""
        } else {
            var unique_species = state_data[state].map(function(value,index) {
                return value[4];
            }).filter(function(v, i, self){
                return i == self.indexOf(v);
            })
            var total_individuals = 0
            state_data[state].forEach(r => {
                total_individuals += parseInt(r[5])
            })
            var unique_observers = state_data[state].map(function(value,index) {
                return value[0] + "-" + value[6]
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
            state_data[state].forEach(r => {
                if(cleaned_rows[r[4]] == undefined){
                    cleaned_rows[r[4]] = {
                        "scientific_name": r[4],
                        "common_name": r[3],
                        "individuals": parseInt(r[5]),
                        "names": r[0],
                        "source": r[6]
                    }
                } else {
                    cleaned_rows[r[4]].individuals += parseInt(r[5])
                    if(!cleaned_rows[r[4]].source.includes(r[6]))
                        cleaned_rows[r[4]].source += ", " + r[6]
                    if(!cleaned_rows[r[4]].names.includes(r.names))
                        cleaned_rows[r[4]].names += ", " + r[0]
                }
            })
            var cleaned = Object.values(cleaned_rows).sort(function(a,b) {
                return b.individuals - a.individuals
            })
        }

        Object.values(cleaned).forEach(p => {table += "<tr><td>" + p.common_name + "</td><td>" + p.scientific_name + "</td><td class='text-center'>" + p.individuals + "</td><td>"+p.source+"</td></tr>"})
        document.getElementById('data-species').innerHTML = unique_species.length
        document.getElementById('data-individuals').innerHTML = total_individuals
        document.getElementById('data-observers').innerHTML = unique_observers.length
        document.getElementById('observers').innerHTML = observers
        document.getElementById('places').innerHTML = state
        document.getElementById('map-data').innerHTML = table
    }

</script>
</body>
</html>
