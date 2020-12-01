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
    var state_data = []
    var processed_data = []

    var largest_total = []
    var label_type = "individuals"

    data.forEach(s => {
        if(state_data[s[7]] == undefined)
            state_data[s[7]] = []
        state_data[s[7]].push(s)
    })

    calculateLabels(label_type)
    renderMap()
    display_data("All")


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
        if(k == label_type)
            element.setAttribute("class", "btn  btn-success")
        else
        element.setAttribute("class", "btn  btn-outline-danger")
        element.setAttribute("onclick", "toggle_button('"+k+"_btn')")

        element.innerHTML = label_button[k]
        document.getElementById("btn_gp").appendChild(element)
    })

    function toggle_button(e){
        if(document.getElementById(e).classList.contains("btn-success")){
            label_type = "All"
            Object.values(document.getElementById("btn_gp").children).forEach(c => {
                document.getElementById(c.id).classList.remove("btn-success")
                document.getElementById(c.id).classList.add("btn-outline-danger")
            })
        } else {
            label_type = e.replace("_btn", "")
            Object.values(document.getElementById("btn_gp").children).forEach(c => {
                if(c.id !== e){
                    document.getElementById(c.id).classList.remove("btn-success")
                    document.getElementById(c.id).classList.add("btn-outline-danger")
                }
            })
            document.getElementById(e).classList.remove("btn-outline-danger")
            document.getElementById(e).classList.add("btn-success")
        }
        calculateLabels(label_type)
        renderMap()
        display_data("state_name")
    }

    function calculateLabels(label_type){
        largest_unique = 0
        largest_individual = 0
        largest_observers = 0
        overall_observers = []
        overall_taxa = []
        overall_individuals = 0

        country.features.forEach(state=> {
            state_name = state.properties.ST_NM
            state_total = 0
            state_observers = []
            cleaned_rows = []
            cleaned = []

            if(state_data[state_name] != undefined){
                state_data[state_name].forEach(r => {
                    if(cleaned_rows[r[4]] == undefined){
                        cleaned_rows[r[4]] = {
                            "scientific_name": r[4],
                            "common_name": r[3],
                            "individuals": parseInt(r[5]),
                            "names": r[0] + " - " + r[6],
                            "source": r[6]
                        }
                    } else {
                        cleaned_rows[r[4]].individuals += parseInt(r[5])
                        if(!cleaned_rows[r[4]].source.includes(r[6]))
                            cleaned_rows[r[4]].source += ", " + r[6]
                        if(!cleaned_rows[r[4]].names.includes(r[0] + " - " + r[6]))
                            cleaned_rows[r[4]].names += ", " + r[0] + " - " + r[6]
                    }
                })
                cleaned = Object.values(cleaned_rows).sort(function(a,b) {
                    return b.individuals - a.individuals
                })

                 Object.values(cleaned_rows).forEach(r => {
                    state_total += parseInt(r.individuals)
                    if(!state_observers.includes(r.names))
                        state_observers.push(r.names)
                })

                if(cleaned.length > largest_unique)
                    largest_unique = cleaned.length
                if(state_total > largest_individual)
                    largest_individual = state_total
                if(state_observers.length > largest_observers)
                    largest_observers = state_observers.length
            }

            processed_data[state_name] = {
                "unique_taxa": cleaned.length,
                "individuals": state_total,
                "state_name": state_total,
                "observers": state_observers.length,
                "species_rows": cleaned,
                "unique_observers": state_observers
            }
        // overall_taxa = []
        // overall_individuals = 0
            if(overall_observers.length == 0)
                overall_observers = state_observers
            else {
                state_observers.forEach(so => {
                    if(!overall_observers.includes(so))
                        overall_observers.push(so)
                })
            }
            if(overall_taxa.length == 0)
                overall_taxa = cleaned
            else {
                cleaned.forEach(c => {
                    match_flag = false
                    overall_taxa.forEach((t,id) => {
                        if(c.scientific_name == t.scientific_name){
                            match_flag = true
                            if(t.common_name.length = 0 && c.common_name.length > 0)
                                overall_taxa[id].common_name = c.common_name
                            overall_taxa[id].individuals += parseInt(c.individuals)
                            if(!t.names.includes(c.name))
                                overall_taxa[id].names += ", " + c.names
                            if(!t.source.includes(c.source))
                                overall_taxa[id].source += ", " + c.source
                        }
                    })

                })
            }
            // console.log(overall_taxa)
        })
            processed_data["All"] = {
                "unique_taxa": cleaned.length,
                "individuals": state_total,
                "state_name": state_total,
                "observers": overall_observers.length,
                "species_rows": cleaned,
                "unique_observers": overall_observers
            }

            largest_total["state_name"] = largest_individual
            largest_total["unique_taxa"] = largest_unique
            largest_total["individuals"] = largest_individual
            largest_total["observers"] = largest_observers
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
        const colors = d3.scaleLinear().domain([0, 1, largest_total[label_type]]).range(["#ccc", "#c95", "#5e9"])
        var legend = d3.legendColor().scale(colors).shapeWidth(55).labelFormat(d3.format(".0f")).orient('horizontal').cells(6)



        base = svg.append("g")
            .classed("map-boundary", true)

        base_text = base.selectAll("text").append("g")
        base = base.selectAll("path").append("g")

        country.features.forEach(state=> {
            if(processed_data[state.properties.ST_NM] == undefined)
                processed_data[state.properties.ST_NM] = 0
            x = base.append("g")
                .data([state])
                .enter().append("path")
                .attr("d", path)
                .attr("stroke", "#333")
                .attr("id", (d) => d.properties.ST_NM)
                .attr("stroke-width", .5)
                .on("click", (d) => display_data(d.properties.ST_NM))
                .attr("fill", (d) => colors(processed_data[d.properties.ST_NM][label_type]))

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
            else
                x.text((d) => processed_data[d.properties.ST_NM][label_type])
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
        observers = "";
        console.log(state)
        processed_data[state].unique_observers.forEach(o => {
            if(o.includes("ibp"))
                observers += '<div class="col text-nowrap border border-warning table-warning text-center rounded m-1">'+o.replace("-ibp", "")+'</div>'
            else if (o.includes("inat"))
                observers += '<div class="col text-nowrap border border-success table-success text-center rounded m-1">'+o.replace("-inat", "")+'</div>'
            else
                observers += '<div class="col text-nowrap border border-info table-info text-center rounded m-1">'+o.replace("-butterfly_counts", "")+'</div>'
        })
        Object.values(processed_data[state].species_rows).forEach(p => {table += "<tr><td>" + p.common_name + "</td><td>" + p.scientific_name + "</td><td class='text-center'>" + p.individuals + "</td><td>"+p.source+"</td></tr>"})
        document.getElementById('data-species').innerHTML = processed_data[state].unique_taxa
        document.getElementById('data-individuals').innerHTML = processed_data[state].individuals
        document.getElementById('data-observers').innerHTML = processed_data[state].observers
        document.getElementById('observers').innerHTML = observers
        document.getElementById('places').innerHTML = state
        document.getElementById('map-data').innerHTML = table
    }

</script>
</body>
</html>
