 function plotHex(sk1, s1, sk2, s2, sk3, s3, sk4, s4, sk5, s5, sk6, s6){
    var vis = d3.select("#hexagon").append("svg")
   		  .attr("width", 640)
         	.attr("height", 533.6),

    scaleX = d3.scale.linear()
   		 .domain([-30,30])
        	.range([0,480]),

    scaleY = d3.scale.linear()
   		 .domain([0,50])
        	.range([400,0]),

    color = d3.scale.category10();

    // Center point for hexagon
    var r = 23.0;
    var x = 13.0;
    var y = 16.0;    	 

    // base for comparison base(rock star info)
    // temp for comparison target(user info)
    var base1 = 100.0; var temp1 = 24.0;
    var base2 = 100.0; var temp2 = 50.0;
    var base3 = 100.0; var temp3 = 72.0;
    var base4 = 100.0; var temp4 = 83.0;
    var base5 = 100.0; var temp5 = 72.0;
    var base6 = 100.0; var temp6 = 62.0;

    // Hexagon vertices for user info
    var ratio1 = s1 / base1 *100;
    var ratio2 = s2 / base2 *100;
    var ratio3 = s3 / base3 *100;
    var ratio4 = s4 / base4 *100;
    var ratio5 = s5 / base5 *100;
    var ratio6 = s6 / base6 *100;


    var arrayOfPolygons =  [{
    	"name": "Maximum Skill Level",
    	"points":[
      	{"x": x,                  	"y": y + r},
      	{"x": x + Math.sqrt(3) * r/2, "y": y + r/2},
      	{"x": x + Math.sqrt(3) * r/2, "y": y - r/2},
      	{"x": x,                  	"y": y - r},
      	{"x": x - Math.sqrt(3) * r/2, "y": y - r/2},
      	{"x": x - Math.sqrt(3) * r/2, "y": y + r/2} 	 
   	]
      },
      {
    	"name": "My Skill Level",
    	"points":[
      	{"x": x,                           	"y": y + r * ratio1},
      	{"x": x + Math.sqrt(3) * r/2 * ratio2, "y": y + r/2 * ratio2},
      	{"x": x + Math.sqrt(3) * r/2 * ratio3, "y": y - r/2 * ratio3},
      	{"x": x,                           	"y": y - r * ratio4 },
      	{"x": x - Math.sqrt(3) * r/2 * ratio5, "y": y - r/2 * ratio5},
      	{"x": x - Math.sqrt(3) * r/2 * ratio6, "y": y + r/2 * ratio6} 	 
     	]   
      }
    ];

    vis.selectAll("polygon")
    	.data(arrayOfPolygons)
    	.enter().append("polygon")
    	.attr("points",function(d) {
        	return d.points.map(function(d) { return [scaleX(d.x),scaleY(d.y)].join(","); }).join(" ");})
	 	.attr("fill", function(d){return color(d.name)})
    .attr("fill-opacity", 0.9)
    	.attr("stroke","#666")
    	.attr("stroke-width",2);


    // add legend   
    var legend = vis.append("g")
      .attr("class", "legend")
    	//.attr("x", w - 65)
    	//.attr("y", 50)
      .attr("height", 100)
      .attr("width", 100)
      .attr('transform', 'translate(10,20)');
     	 
    legend.selectAll('rect')
      	.data(arrayOfPolygons)
      	.enter()
      	.append("rect")
      	.attr("x", 40)
      	.attr("y", function(d, i){ return i *  20;})
      	.attr("width", 10)
      	.attr("height", 10)
          	.style("fill", function(d) { return color(d.name);});
 	 
    legend.selectAll('text')
      	.data(arrayOfPolygons)
      	.enter()
      	.append("text")
      	.attr("x", 60)
      	.attr("y", function(d, i){ return i *  20 + 9;})
      	.text(function(d) {return d.name});

//---------------------------------------------------add text------------------------//
    vis.append("text")
            .attr('x', 320)
            .attr('y', 80)
            .text(sk1);

    vis.append("text")
            .attr('x', 510)
            .attr('y', 190)
            .text(sk2);

    vis.append("text")
            .attr('x', 510)
            .attr('y', 370)
            .text(sk3);

    vis.append("text")
            .attr('x', 320)
            .attr('y', 480)
            .text(sk4);

    vis.append("text")
            .attr('x', 140)
            .attr('y', 370)
            .text(sk5);

    vis.append("text")
            .attr('x', 140)
            .attr('y', 190)
            .text(sk6);
}

