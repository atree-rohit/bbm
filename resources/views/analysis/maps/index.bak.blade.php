@extends('layouts.app')

@section('style')
    <script src="{{ asset('js/d3.min.js') }}"></script>
    <script src="{{ asset('js/h3-js.js') }}"></script>
    <script src="{{ asset('js/country.js') }}"></script>
@endsection

@section('content')
    <div class="bg-dark border border-danger p-3 d-flex">
        <div id="map-container" class="svg-container bg-light m-auto"></div>
    </div>

@endsection

@section('script')
    <script>
    const svgWidth = 800;
    const svgHeight = 850;
    var current_zoom = 4;
    renderMap(current_zoom);

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

function renderMap(h3_zoom) {
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
    var places = @json($forms);

    const light = ["#2979ff", "#2979ff"];

    const palettes = [light];
    const lightGreenFirstPalette = palettes
        .map(d => d.reverse())
        .reduce((a, b) => a.concat(b));
    const color = d3.scaleLinear(lightGreenFirstPalette);



    svg.append("g")
        .classed("map-boundary", true)
        .selectAll("path").append("g")
        .data(country.features)
        .enter().append("path")
        .attr("d", path)
        .attr("stroke", "#999")
        .attr("stroke-width", .3)
        .style("z-index", 10)
        .attr("fill", "none");

    renderHex(svg, path, country, places, h3_zoom);

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

function renderHex(svg, path, country, places, h3_zoom)
{
    const hexagons = h3.polyfill(country.features[0].geometry.coordinates[0].slice(0, -1).map(d => [d[1], d[0]]), h3_zoom);
    const coordinates = h3.h3SetToMultiPolygon(hexagons, true);
    const label_size = h3_zoom*1.2;
    const hexColor = {
        1: "#D7F4D2",
        2: "#BFEEB7",
        3: "#A7E997",
        4: "#77DD66"
    };

    console.log(hexColor);
    d3.select(".hex-content").remove();

    const h3Hexes = svg.append("g").classed("hex-content", true).selectAll("path");
    const h3_zoom_factor = 5-h3_zoom;

    var h3Hex = [];
    places.forEach(p => {
        const h3Address = h3.geoToH3(p.latitude, p.longitude, h3_zoom);
        var matchFlag = false;
        h3Hex.forEach((h,i) => {
            if (h.hexID == h3Address) {
                h.value += p.pointCount;
                matchFlag = true;
                matchID = i;
            }
        });
        if (matchFlag == false) {
            const h3Boundary = h3.h3ToGeoBoundary(h3Address, true);
            const h3Geo = hexFeatures(h3Boundary);
            h3Hex.push({ "hexID": h3Address, "counts":1 ,"names":p.name, "species_count": p.species, "total": p.total, "coordinates": h3Geo });
            // h3Hex.push({ "hexID": h3Address, "value": p.latitude +','+ p.longitude, "coordinates": h3Geo });
        }
        else {
            h3Hex[matchID].counts += 1;
            h3Hex[matchID].names += ", " + p.name;
            h3Hex[matchID].species_count += p.species;
            h3Hex[matchID].total += p.total;
        }
    });
    const display_field = "species_count";
    // const display_field = "counts";

    h3Hex.forEach(h => {
        const x = h3Hexes.data(h.coordinates.features)
        const y = x.enter().append("g")
        const digits = h[display_field].toString().length

        y.append("path")
            .attr("d", path)
            .attr("stroke-width", ".3")
            .attr("stroke", "#5a5")
            .attr("opacity", ".70")
            .attr("fill", hexColor[digits]);

        y.append("text")
            .attr("x", function(h) { return path.centroid(h)[0]; })
            .attr("y", function(h) { return path.centroid(h)[1]; })
            // .attr("dx", -1*((20*digits)/(h3_zoom+1)))
            .attr("dx", -1*(digits*1.5))
            // .attr("dy", -1*(25/(h3_zoom+1)))
            .attr("alignment-baseline", "central")
            .style("font-size", label_size)
            .style("font-weight", "bold")
            .style("fill", "#000")
            .text(h[display_field]);
            // .text(h.names);

        // y.append("text")
        //     .attr("x", function(h) { return path.centroid(h)[0]; })
        //     .attr("y", function(h) { return path.centroid(h)[1]; })
        //     .attr("dx", -2*(50/(h3_zoom+1)))
        //     .attr("dy", 2.5*(25/(h3_zoom+1)))
        //     .attr("alignment-baseline", "central")
        //     .style("font-size", label_size-4)
        //     .style("fill", "#ff0000")
        //     .text(h.species_count);

        // y.append("text")
        //     .attr("x", function(h) { return path.centroid(h)[0]; })
        //     .attr("y", function(h) { return path.centroid(h)[1]; })
        //     .attr("dx", -1*Math.pow(digits+3,1.5) )
        //     .attr("alignment-baseline", "central")
        //     .style("font-size", 13)
        //     .style("fill", "#000000")
        //     .text(h.total);


    });
}


    </script>
@endsection
