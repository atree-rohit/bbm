<!DOCTYPE html>
<html>
<head>
    <title></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/5.9.2/d3.min.js" integrity="sha512-U3BExhsSSzrscvztemnZwpCsZfbKnEFXOIezIrAi3y7sjiALdlRQsknCyp/KBKnaJZxjQXvLBT4UPkyq06BnJA==" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3-legend/2.25.6/d3-legend.min.js"></script>
    <script src="{{ asset('js/country_n_r.js') }}"></script>
    <script src="{{ asset('js/states_data.js') }}"></script>
    <style>
        .selected_polygon{
            fill: #ffc;
        }
        .poly_text{
            z-index:100;
            /*stroke:#222 !important;*/
            fill: #111;
        }
        .map-boundary path:hover{
            cursor: pointer;
            fill: yellow;
            stroke:red;
        }
        .map-boundary path.selected_polygon:hover{
            fill: #f55;
        }
        .state-table tbody tr:hover{
            background-color: #ff9 !important;
            cursor: pointer;
        }
        /*width*/
        ::-webkit-scrollbar {
          width: 7px;
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

        <div id="map-container" class="svg-container bg-light col-xl-6"></div>

        <div class="col-xl-6 table-info" style="max-height: 95vh;overflow-y: none;">
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
            <div style="max-height: 40vh;overflow-y: scroll;" class="border border-info">
                <table class="table table-sm table-striped bg-light">
                    <thead id="data-table" class="bg-primary text-light"></thead>
                    <tbody id="map-data"></tbody>
                </table>
            </div>
        </div>
    </div>
<script>

    var svgWidth = window.innerWidth / 2
    if(window.innerWidth < 1140){
        alert(window.innerWidth)
        svgWidth = window.innerWidth - 50
    }
    const svgHeight = window.innerHeight - 30
    var svg = 0
    var path = 0

    var largest_total = {}
    var state_stats = {}
    var label_type = "individuals"
    const label_button = {
        "state_name": "State Name",
        "unique_taxa": "Unique Taxa",
        "individuals": "Individuals",
        "unique_observers": "Observers"
    }



    Object.keys(states_data).forEach(state => {
        s = states_data[state]
        rows = []
        s[3].forEach(r => {
            rows.push({
                "scientific_name": r[0],
                "common_name": r[1],
                "individuals": r[2],
                "names": r[3],
                "source": r[4],
            })
        })
        state_stats[state] = {
            "unique_taxa": s[0],
            "individuals": s[1],
            "unique_observers": s[2],
            "species_rows": rows
        }
        Object.keys(label_button).forEach(col => {
            if(col != "state_name" && state != "state_name")
                if(col == "unique_observers"){
                    if(largest_total[col] == undefined || largest_total[col] < state_stats[state][col].length)
                        largest_total[col] = state_stats[state][col].length
                } else {
                    if(largest_total[col] == undefined || largest_total[col] < state_stats[state][col])
                        largest_total[col] = state_stats[state][col]
                }
        })
        largest_total["state_name"] = largest_total["individuals"]
    })
    


    Object.keys(label_button).forEach(k=>{
        var element = document.createElement("button");
        element.type = "button"
        element.value = k
        element.id = k + "_btn"
        if(k == label_type)
            element.setAttribute("class", "btn  btn-success")
        else
        element.setAttribute("class", "btn  btn-outline-danger")
        element.setAttribute("onclick", "toggle_labels('"+k+"_btn')")

        element.innerHTML = label_button[k]
        document.getElementById("btn_gp").appendChild(element)
    })

    function toggle_labels(e){
        label_type = e.replace("_btn", "")
        Object.values(document.getElementById("btn_gp").children).forEach(c => {
            if(c.id !== e){
                document.getElementById(c.id).classList.remove("btn-success")
                document.getElementById(c.id).classList.add("btn-outline-danger")
            }
        })
        document.getElementById(e).classList.remove("btn-outline-danger")
        document.getElementById(e).classList.add("btn-success")
        display_data("state_name")
    }

    display_data("state_name")

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
        const colors = d3.scaleLinear().domain([0, 1, largest_total[label_type]]).range(["#f77", "#6a8", "#7f9"])
        var legend = d3.legendColor().scale(colors).shapeWidth(55).labelFormat(d3.format(".0f")).orient('horizontal').cells(6)

        base = svg.append("g")
            .classed("map-boundary", true)

        base_text = base.selectAll("text").append("g")
        base = base.selectAll("path").append("g")

        country.features.forEach(state=> {
            var s_name = state.properties.st_nm
            
            shape = base.append("g")
                .data([state])
                .enter().append("path")
                .attr("d", path)
                .attr("stroke", "#333")
                .attr("id", s_name)
                .attr("stroke-width", .5)
                .on("click", (d) => display_data(s_name))
            if(label_type == "state_name")
                shape.attr("fill", (d) => colors(state_stats[s_name]["individuals"]))
            else if (label_type == "unique_observers")
                shape.attr("fill", (d) => colors(state_stats[s_name][label_type].length))
            else
                shape.attr("fill", (d) => colors(state_stats[s_name][label_type]))
        })
        country.features.forEach(state=> {
            var s_name = state.properties.st_nm

            label = base_text.append("g")
                .data([state])
                .enter().append("text")
                    .classed("poly_text", true)
                    .attr("x", (h) => path.centroid(h)[0] )
                    .attr("y", (h) => path.centroid(h)[1] )
                    .attr("text-anchor", "middle")
            if(label_type == "state_name")
                label.attr("font-size",9)
                    .text(s_name)
            else if (label_type == "unique_observers")
                label.attr("font-size",12)
                    .text(state_stats[s_name][label_type].length)
            else
                label.attr("font-size",12)
                    .text(state_stats[s_name][label_type])
            label.on("click", (d) => display_data(s_name))

        })

        svg.append("g")
            .attr("transform", "translate("+svgWidth*.575+", 50)")
            .call(legend)
            .append("text")
                .classed("map_label", true)
                .attr("dx", 5)
                .attr("dy", -10)
                .classed("h1", true)
                .text(label_type.replace(/(?:_| |\b)(\w)/g, function($1){return $1.toUpperCase().replace('_',' ');}))

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
        renderMap()
        table = ""
        observers = ""
        Object.values(document.getElementsByClassName("map-boundary")[0].children).forEach(p => {
            if(p.id === state)
                document.getElementById(p.id).classList.add("selected_polygon")
            else if(document.getElementById(p.id) != null)
                document.getElementById(p.id).classList.remove("selected_polygon")
        })
        if(state == "state_name"){
            heading_name = "States Summary"
            table_header = "<tr><th>State Name</th><th>Unique Taxa</th><th>Individuals</th><th>Observers</th><th>Sources</th></tr>"

            sorted_states = Object.keys(state_stats).sort().forEach(s => {
                if(s != "state_name"){
                    d = state_stats[s]
                    sources = []
                    d.species_rows.forEach(r => {
                        r.source.split(", ").forEach(cs => {
                            if(!sources.includes(cs))
                                sources.push(cs)
                        })
                            
                    })
                    select_fn = "onclick='display_data(" + '"'+s+'"'+")'"
                    table += "<tr "+select_fn+"><td>" + s + "</td><td>" + d.unique_taxa + "</td><td>" + d.individuals + "</td><td>" + d.unique_observers.length + "</td><td>" + sources.join(", ") + "</td></tr>"                    
                }
            })
            document.getElementById('data-table').parentElement.classList.add("state-table")
        } else {
            document.getElementById('data-table').parentElement.classList.remove("state-table")
            back_btn = "onclick='display_data(" + '"state_name"'+")'"
            heading_name = "<button class='btn btn-sm btn-outline-primary mr-5' "+back_btn+" >Back to States Summary</button>" + state
            table_header = "<tr><th>Common Name</th><th>Scientific Name</th><th>Individuals</th><th>Source</th></tr>"
            Object.values(state_stats[state].species_rows).forEach(p => {table += "<tr><td>" + p.common_name + "</td><td>" + p.scientific_name + "</td><td class='text-center'>" + p.individuals + "</td><td>"+p.source+"</td></tr>"})
        }
        state_stats[state].unique_observers.forEach(o => {
            if(o.includes("ibp"))
                observers += '<div class="col text-nowrap border border-warning table-warning text-center rounded m-1">'+o.replace("-ibp", "")+'</div>'
            else if (o.includes("inat"))
                observers += '<div class="col text-nowrap border border-success table-success text-center rounded m-1">'+o.replace("-inat", "")+'</div>'
            else
                observers += '<div class="col text-nowrap border border-info table-info text-center rounded m-1">'+o.replace("-counts", "")+'</div>'
        })
        document.getElementById('data-species').innerHTML = state_stats[state].unique_taxa
        document.getElementById('data-individuals').innerHTML = state_stats[state].individuals
        document.getElementById('data-observers').innerHTML = state_stats[state].unique_observers.length
        document.getElementById('observers').innerHTML = observers
        document.getElementById('places').innerHTML = heading_name
        document.getElementById('data-table').innerHTML = table_header
        document.getElementById('map-data').innerHTML = table
    }

    // querySelectorAll

</script>
</body>
</html>
