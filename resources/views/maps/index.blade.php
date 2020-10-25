@extends('layouts.app')

@section('style')
    <script src="{{ asset('js/d3.min.js') }}"></script>
    <script src="{{ asset('js/h3-js.js') }}"></script>
    <script src="{{ asset('js/country.js') }}"></script>
@endsection

@section('content')

    <div id="map-container" class="svg-container bg-light"></div>

@endsection

@section('script')
    <script>
    const svgWidth = 1600;
    const svgHeight = 1200;
    var current_zoom = 2;
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
        .attr("viewBox", "0 0 " + svgWidth + " " + svgHeight)
        .classed("svg-content", true);
    var projection = d3.geoMercator().scale(1200).center([65, 28]);
    var path = d3.geoPath().projection(projection);
    var places = @json($forms);
    var label_size = 7;


    // const dark = ['#B08B12', '#BA5F06', '#8C3B00', '#6D191B', '#842854', '#5F7186', '#193556', '#137B80', '#144847', '#254E00'];
    // const mid = ['#E3BA22', '#E58429', '#BD2D28', '#D15A86', '#8E6C8A', '#6B99A1', '#42A5B3', '#0F8C79', '#6BBBA1', '#5C8100'];
    // const light = ['#F2DA57', '#F6B656', '#E25A42', '#DCBDCF', '#B396AD', '#B0CBDB', '#33B6D0', '#7ABFCC', '#C8D7A1', '#A0B700'];
    const light = ["#2979ff", "#2979ff"];

    const palettes = [light];
    const lightGreenFirstPalette = palettes
        .map(d => d.reverse())
        .reduce((a, b) => a.concat(b));
    const color = d3.scaleLinear(lightGreenFirstPalette);


    const hexagons = h3.polyfill(country.features[0].geometry.coordinates[0].slice(0, -1).map(d => [d[1], d[0]]), h3_zoom);

    // Get the outline of a set of hexagons, as a GeoJSON-style MultiPolygon
    const coordinates = h3.h3SetToMultiPolygon(hexagons, true);
    const mapBoundary = makeGeoJSON_reverse(coordinates[0]);


    var h3Hex = [];
    const h3Hexes = svg.append("g").selectAll("path");
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
            h3Hex[matchID].counts++;
            h3Hex[matchID].names += ", " + p.name;
            h3Hex[matchID].species_count += p.species;
            h3Hex[matchID].total += p.total;
        }
    });
    console.log(h3Hex);

    h3Hex.forEach(h => {
        const x = h3Hexes.data(h.coordinates.features);
        x.enter().append("path")
            .attr("d", path)
            // .attr("stroke", "red")
            // .attr("stroke-width", 1)
            .attr("opacity", ".25")
            .attr("fill", "red");

            x.enter().append("text")
                .attr("x", function(h) { return path.centroid(h)[0] - 8; })
                .attr("y", function(h) { return path.centroid(h)[1] - 6; })
                .attr("alignment-baseline", "central")
                .style("font-size", label_size)
                .style("fill", "#000000")
                .text(h.counts);

            x.enter().append("text")
                .attr("x", function(h) { return path.centroid(h)[0] + 3; })
                .attr("y", function(h) { return path.centroid(h)[1] - 6; })
                .attr("alignment-baseline", "central")
                .style("font-size", label_size)
                .style("fill", "#ff0000")
                .text(h.species_count);

            x.enter().append("text")
                .attr("x", function(h) { return path.centroid(h)[0] - 5; })
                .attr("y", function(h) { return path.centroid(h)[1] + 8; })
                .attr("alignment-baseline", "central")
                .style("font-size", label_size)
                .style("fill", "#009900")
                .text(h.total);

            // x.enter().append("text")
            //     .attr("x", function(h) { return path.centroid(h)[0] - 15; })
            //     .attr("y", function(h) { return path.centroid(h)[1] + 18; })
            //     .attr("alignment-baseline", "central")
            //     .style("font-size", label_size)
            //     .style("fill", "#009900")
            //     .text(h.names);    
            ;
            // .on("click", console.log(h.value_name));
            // (d) => { ons.notification.toast(`${h.value_counts} | ${h.value_species} | ${h.value_total} `, { timeout: 2000, animation: 'lift' }) }

            // x
    });

    svg.append("g").selectAll("path").append("g")
        .data(country.features)
        .enter().append("path")
        .attr("d", path)
        .attr("stroke", "#999999")
        .attr("stroke-width", .5)
        .style("z-index", 10)
        .attr("fill", "none");


    var zoom = d3.zoom()
        .scaleExtent([1, 30])
        .on('zoom', function() {
            svg.selectAll('circle')
                .attr('transform', d3.event.transform),
            svg.selectAll('text')
                .attr('transform', d3.event.transform),
            svg.selectAll('path')
                .attr('transform', d3.event.transform);
            k = d3.event.transform.k;
            if(Math.abs(current_zoom - Math.floor(k+1)) > 1){
                current_zoom = Math.floor(k+2)
                renderMap(current_zoom);
            }
            console.log(current_zoom, Math.floor(k+1));
        });

    svg.call(zoom);

}


    </script>
@endsection
