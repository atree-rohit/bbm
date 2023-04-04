<style>
    #controls{
        margin: 0 2rem;
    }
	.map-boundary path{
		/* stroke: transparent; */
		stroke-linejoin: round;
		stroke-width: .25;
		stroke:rgba(0, 0, 0, 0.5);
	}
	.map-boundary path:hover{
		cursor: pointer;
		fill: beige;
	}
	.map-boundary .current-state{
		stroke: rgba(0,50,255,.75);
		stroke-width:.25px;
		filter: brightness(1.25)
	}
	.map-boundary .selected-polygon{
		/*fill: #afa;*/
		fill: #ffff55;
		stroke: rgba(255,50,0,.75);
		stroke-width:.5px;
	}
	.poly_text{
		fill: #545;
		font-size: 1.0rem;
		transition: fill .125s;
		text-shadow: 
		0px 0px 1px white,
		0px 0px 2px white,
		0px 0px 3px white,
		0px 0px 4px white,
		0px 0px 5px white;
	}
	.poly_text:hover{
		fill: #00c;
		text-shadow: 0px 0px 5px #fff;
		cursor: pointer;
		font-weight: 1000;
	}
    .map-points circle{
		stroke-width: .5px;
		stroke: rgba(0,0,0,.25);
		fill: transparent;
	}
	.map-points circle:hover{
		cursor:pointer;
		stroke: rgba(0,255,0,.5);
	}
	svg{
		background: hsl(200, 50%, 75%);
	}
    table, tr, th,td{
        border: 1px solid white;
    }
    td {
        padding: 0.5rem 1rem;
    }
	@media screen and (max-width: 800px) {
		.poly_text{
			font-size: 3.5vw;
		}
	}
</style>

<template>
	<div>
        <div id="controls">
            <h3>{{mapModes[mapMode]}} - {{selected_area}}</h3>
			<div class="row">
				<input
					type="range"
					class="form-range"
					min="0"
					max="2"
					v-model="mapMode"
				>
			</div>
        </div>
		<div id="map-container"></div>
	</div>
</template>

<script lang="ts">
import * as d3Legend from "d3-svg-legend"
import * as d3 from "d3"
import regions from '../geojson/regions.json'
import states from '../geojson/states.json'
import districts from '../geojson/districts_2023.json'
import { mapState } from 'vuex'
import store from '../store/'
export default {
	name:"IndiaMap",
	data() {
		return{
            mapMode: 0,
            mapModes: ["Region", "State", "District"],
			mapLayers: [regions, states, districts],
			polygons: null,
			path: null,
			svg: {},
			projection: {},
			colors: {},
			legend: {},
			state_data: {},
			selected_area:"All",
			max: 0,
			state_max: 0,
			height: window.innerHeight * 0.75,
			width: window.innerWidth * 0.95,
			tooltip:null,
            regions: {
				"east":["Arunachal Pradesh","Assam","Manipur","Meghalaya","Mizoram","Nagaland","Sikkim","Tripura","Bihar","Odisha","Jharkhand","West Bengal"],
				"north":["Chandigarh","Punjab","Uttarakhand","Ladakh","Jammu & Kashmir","Delhi","Haryana","Himachal Pradesh","Uttar Pradesh"],
				"south":["Karnataka","Telangana","Kerala","Andaman & Nicobar","Lakshadweep","Tamil Nadu","Andhra Pradesh","Puducherry"],
				"west":["Rajasthan","Madhya Pradesh","Gujarat","Dadra,Nagar Haveli,Daman & Diu","Chhattisgarh","Goa","Maharashtra"]
			},
			area_names: {
				region: [],
				state: [],
				district: []
			},
			observation_counts: {},
			hexagons: {},
			locations: [],
			areaStats: {},
		}
	},
	mounted(){
		this.init_tooltip()
		this.init_area_names()
		this.init()
		console.clear()
	},
	computed:{
		...mapState({
			selected: state => state.selected,
			filtered_observations: state => state.filtered_observations,
		}),
		zoom() {
			return d3.zoom()
				.scaleExtent([.5, 250])
				.translateExtent([[-0.5 * this.width,-0.75 * this.height],[2.5 * this.width, 2.5 * this.height]])
				.on('zoom', this.handleZoom)
		},
	},
	watch: {
		filtered_observations(){
			this.init()
		},
		mapMode(){
			this.selected_area = "All"
			this.init()
		}
	},
	methods:{
        capitalizeWords(str){
			return str ? str.charAt(0).toUpperCase() + str.slice(1) : ""
		},
        color_polygon(polygon) {
			let mode = this.mapModes[this.mapMode].toLowerCase()
            let op
			if(this.mapMode < 3){
				console.log(this.areaStats[mode], polygon[mode])
				op = this.colors(this.areaStats[mode][polygon[mode]].observations)
			} else {
				op = 'hsl(200,100%, 80%)'
			}
			
            return op
        },
        handleZoom(e){
			let text_size = (1/e.transform.k)
            this.svg.selectAll('.poly_text')
                .attr('transform', e.transform)
				.style('font-size', `${text_size}rem`)
            this.svg.selectAll('path')
                .attr('transform', e.transform)
            this.svg.selectAll('circle')
                .attr('transform', e.transform)
				.attr("r", text_size)
        },
		init () {
			this.polygons = null
			this.path = null
			this.svg = {}
			this.height = window.innerHeight * 0.75
			this.width = window.innerWidth * 0.95
			if(window.innerWidth < 800){
				this.projection = d3.geoMercator().scale(700).center([106.75, 27.5])
			} else {
				this.projection = d3.geoMercator().scale(1000).center([65, 27.5])
			}
			this.path = d3.geoPath().projection(this.projection)
			this.init_area_stats()
			this.init_legend()
			this.renderMap()
		},
		init_area_stats(){
			["region", "state", "district"].map((s) => this.areaStats[s] = {})
			this.mapLayers[2].features.forEach((district) => {
				let props = district.properties
				if(this.areaStats.region[props.region] == undefined){
					this.areaStats.region[props.region] = {observations:0, users:new Set(), species: new Set()}
				}
				if(this.areaStats.state[props.state] == undefined){
					this.areaStats.state[props.state] = {observations:0, users:new Set(), species: new Set()}
				}
				if(this.areaStats.district[props.district] == undefined){
					this.areaStats.district[props.district] = {observations:0, users:new Set(), species: new Set()}
				}
			})
			let data = this.filtered_observations.filter((d) => d.district != null)
			d3.groups(data, o => o.state, o => o.district).map((state) => {
				let region = ""
				Object.keys(this.regions).forEach((r) => {
					if(this.regions[r].indexOf(state[0]) != -1){
						region = r
					}
				})
				state[1].map((district) => {
					if(this.areaStats.state[state[0]] == undefined){
						console.log(2, state)
					}
					this.areaStats.district[district[0]].observations = district[1].length
					this.areaStats.district[district[0]].users = new Set(district[1].map((o) => o.user_id))
					this.areaStats.district[district[0]].species = new Set(district[1].map((o) => o.taxa_id))
					this.areaStats.state[state[0]].observations += this.areaStats.district[district[0]].observations
					this.areaStats.state[state[0]].users = new Set([...this.areaStats.state[state[0]].users, ...this.areaStats.district[district[0]].users])
					this.areaStats.state[state[0]].species = new Set([...this.areaStats.state[state[0]].species, ...this.areaStats.district[district[0]].species])
					this.areaStats.region[region].observations += this.areaStats.district[district[0]].observations
					this.areaStats.region[region].users = new Set([...this.areaStats.region[region].users, ...this.areaStats.district[district[0]].users])
					this.areaStats.region[region].species = new Set([...this.areaStats.region[region].species, ...this.areaStats.district[district[0]].species])
				})
			})
			Object.keys(this.areaStats).forEach((m) => {
				Object.keys(this.areaStats[m]).forEach((a) => {
					this.areaStats[m][a].users = this.areaStats[m][a].users.size ? this.areaStats[m][a].users.size : 0
					this.areaStats[m][a].species = this.areaStats[m][a].species.size ? this.areaStats[m][a].species.size : 0
				})
			})
			
		},
		get_total_observations(lists){
			let total = 0
			Object.keys(lists).forEach((year) => {
				total += Object.values(lists[year]).reduce((a,b) => a + b.count, 0)
			})
			return total
		},
		get_total_users(lists){
			let users = new Set()
			Object.keys(lists).forEach((year) => {
				Object.values(lists[year]).forEach((list) => {
					list.users.forEach((user) => {
						users.add(user)
					})
				})
			})
			return users
		},
		get_total_species(lists){
			let species = new Set()
			Object.keys(lists).forEach((year) => {
				species = [...species, Object.keys(lists[year])]
			})
			return species
		},
		init_tooltip(){
            this.tooltip = d3.select('body')
							    .append('div')
							    .attr('class', 'd3-tooltip')
							    .style('position', 'absolute')
							    .style('top', '0')
							    .style('z-index', '10')
							    .style('visibility', 'hidden')
							    .style('padding', '10px')
							    .style('background', 'rgba(0,0,0,0.6)')
							    .style('border-radius', '4px')
							    .style('color', '#fff')
							    .text('a simple tooltip')
        },
		init_area_names(){
			["region", "state", "district"].forEach((mode) => {
				this.area_names[mode] = [...new Set(districts.features.map((l) => l.properties[mode]))]
			})
		},
		init_legend() {
			this.colors = {}
			this.legend = {}
			this.max = 0
			if(this.mapMode == 0 ){
				this.max = Math.max(...Object.values(this.areaStats.region).map((r) => r.observations))
				let min = Math.min(...Object.values(this.areaStats.region).map((r) => r.observations))
				this.colors = d3.scaleLinear()
					.domain([min, this.max])
					.range(["#33d", "#3d3"])
			} else if (this.mapMode == 1){
				this.max = Math.max(...Object.values(this.areaStats.state).map(((s) => s.observations)))
				this.colors = d3.scaleLinear()
					.domain([0, 1, this.max*0.1, this.max])
					.range(["#f77", "#ca0", "#cda", "#3d3"])
			} else if (this.mapMode == 2 ){
				this.max = Math.max(...Object.values(this.areaStats.district).map((d) => d.observations))
				this.colors = d3.scaleLinear()
					.domain([0, 1, this.max*.25, this.max])
					.range(["#f77", "#ca0", "#ada", "#3d3"])
			}
			if(this.max == 0){
				this.colors = d3.scaleLinear()
					.domain([0, this.max])
					.range(["#f77", "#f77"])
			}
			this.legend = d3Legend.legendColor()
								.shapeWidth(45)
								.scale(this.colors)
								.labelFormat(d3.format(".0f"))
								.orient('horizontal')
								.labelOffset(-10)
								.labelAlign("start")
								.cells(6)
		},			
		renderMap () {
			if (!d3.select("#map-container svg.svg-content").empty()) {
				d3.select("#map-container svg.svg-content").remove()
			}
			this.svg = d3.select("#map-container")
						.append("svg")
							.attr("preserveAspectRatio", "xMinYMin meet")
							.attr("width", this.width)
							.attr("height", this.height)
							// .style("background-color", "rgb(190, 229, 235)")
							.classed("svg-content d-flex m-auto", true)
			if(this.height > this.width){
				this.legend.shapeWidth(35)
				.cells(4)
				// .shapePadding(37)
			}
			let base = this.svg.append("g")
				.classed("map-boundary", true)
				.selectAll("path").append("g")
			let base_text = this.svg.append("g")
				.classed("map-labels", true)
				.selectAll("text").append("g")
			this.polygons = base.append("g")
				.classed("polygons", true)
			if(this.mapMode == 1 && this.selected_area != "All"){
				console.log("now")
			}
			this.mapLayers[this.mapMode].features.forEach((polygon) => {
				this.drawPolygon(polygon)
			})
			if(this.mapMode < 2){
				this.mapLayers[this.mapMode].features.forEach((polygon) => {
					this.drawPolygonLabel(base_text, polygon)
				})					
			}
			
			this.svg.append("g")
				.attr("transform", "translate("+this.width*.5+", 50)")
				.call(this.legend)
			this.svg.call(this.zoom)
		},
		drawPolygon(polygon){
			this.polygons.append("g")
				.data([polygon])
				.enter().append("path")
				.attr("d", this.path)
				.attr("id", this.getPolygonId(polygon.properties))
				.attr("fill", (d) => this.color_polygon(polygon.properties))
				.on('mouseover', (d, i) => {
					this.tooltip.html(this.hover_text(polygon.properties))
						.style('visibility', 'visible')
				})
				.on('mousemove', (event, d) => {
					this.tooltip
						.style('top', event.pageY - 10 + 'px')
						.style('left', event.pageX + 10 + 'px')
				})
				.on('mouseout', () => this.tooltip.html(``).style('visibility', 'hidden'))
				.on("click", (d) => this.clicked(d))
		},
		drawPolygonLabel(base_text, polygon){
			let region = polygon.properties.region ?? null
			let state = polygon.properties.state ?? null
			let district = polygon.properties.district ?? null
			let name = [region, state, district][this.mapMode]
			let mode = this.mapModes[this.mapMode].toLowerCase()
			let observations = this.areaStats[mode][name].observations
			if(observations > 0) {
				base_text.append("g")
					.data([polygon])
					.enter().append("text")
					.classed("poly_text", true)
					.attr("x", (h) => this.path.centroid(h)[0] )
					.attr("y", (h) => this.path.centroid(h)[1] )
					.classed("small-text" , this.mapMode === 2)
					.attr("text-anchor", "middle")
					.text(observations)
					.on('mouseover', () => {
						this.tooltip.html(this.hover_text(polygon.properties))
							.style('visibility', 'visible');
					})
					.on('mousemove', (event, d) => {
						this.tooltip
							.style('top', event.pageY - 10 + 'px')
							.style('left', event.pageX + 10 + 'px')
					})
					.on('mouseout', () => this.tooltip.html(``).style('visibility', 'hidden'))
					.on("click", this.clicked)
			}
		},
		hover_text(properties){
			let region = properties.region ?? null
			let state = properties.state ?? null
			let district = properties.district ?? null
			let name = [region, state, district][this.mapMode]
			let mode = this.mapModes[this.mapMode].toLowerCase()
			return `<table>
				<tr><td>${this.capitalizeWords(mode)}</td><td>${this.capitalizeWords(name)}</td></tr>
				<tr><td>Observations</td><td>${this.areaStats[mode][name].observations}</td></tr>
				<tr><td>Users</td><td>${this.areaStats[mode][name].users}</td></tr>
				<tr><td>Unique Taxa</td><td>${this.areaStats[mode][name].species}</td></tr>
			</table>`
		},
		getPolygonId(polygon){
			let op = polygon.region
			let replace_chars = [" ", "&", "(", ")", "."]
			if(polygon.state != undefined){
				op = polygon.state
			}
			if(polygon.district != undefined){
				op = polygon.district
			}
			replace_chars.forEach((c) => {
				op = op.replaceAll(c, "_")
			})
			return op
		},
		set_state_class (state, district) {
			this.mapLayers[2].features.map((p) => {
				if(p.properties.state == state && p.properties.district != district){
					d3.select("#" + this.getPolygonId(p.properties)).classed("current-state", true)
				}
			})
		},
		clicked(d) {
			let props = d.target.__data__.properties
			let region = props.region ?? null
			let state = props.state ?? null
			let district = props.district ?? null
			let name = [region, state, district][this.mapMode]
			let polygon = this.mapLayers[this.mapMode].features
			.filter((p) => p.properties[["region", "state", "district"][this.mapMode]] == name)[0]
			this.tooltip.html(``).style('visibility', 'hidden')
            
			let [[x0, y0], [x1, y1]] = [[0,0],[0,0]]
			
			d3.selectAll(".current-state").classed("current-state", false)
			d3.selectAll(".selected-polygon").classed("selected-polygon", false)
			if(this.selected_area == "All" || this.selected_area != name){
				this.selected_area = name;
				[[x0, y0], [x1, y1]] = this.path.bounds(polygon);
				d3.select("#" + this.getPolygonId(props)).classed("selected-polygon", true)
				if(this.mapMode == 2){
					this.set_state_class(state, district)
				}
			} else {
				this.selected_area = "All";
				[[x0, y0], [x1, y1]] = this.path.bounds(regions);
			}
			// this.selectArea()
			
			this.svg.transition().duration(750).call(
				this.zoom.transform,
				d3.zoomIdentity
				.translate(this.width / 2, this.height / 2)
				.scale(Math.min(8, 0.9 / Math.max((x1 - x0) / this.width, (y1 - y0) / this.height)))
				.translate(-(x0 + x1) / 2, -(y0 + y1) / 2),
			)
		},
		dispatchSelectArea(type, name){
			store.dispatch('setSelected', {
				filter: type,
				value: name
			})
		},
		selectArea () {
			if(this.selected_area == "All"){
				this.dispatchSelectArea("state", "All") 
			} else if(this.area_names.state.indexOf(this.selected_area) != -1){
				this.dispatchSelectArea("state", this.selected_area) 
			}
		},
	}
};
</script>