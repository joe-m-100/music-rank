

function drawChart(data) {

    const container = d3.select("#lineChartContainer")
    container.selectAll("*").remove()

    const { width, height } = container.node().getBoundingClientRect();

    // Create SVG element and append it to the chart container

    const svg = container
        .append("svg")
        .attr("width", "100%")
        .attr("height", "100%")
        .attr("viewBox", `0 0 ${width} ${height}`);

    const margin = { top: 10, right: 20, bottom: 40, left: 40 };
    const innerWidth = width - margin.left - margin.right;
    const innerHeight = height - margin.top - margin.bottom;

    const g = svg
        .append("g")
        .attr("transform", `translate(${margin.left},${margin.top})`)


    const dataset_mean = data.mean;
    const dataset_sentiment = data.sentiment;
    const dataset_ratings = data.ratings;

    const x  = d3.scaleLinear()
        .domain([1, dataset_ratings.length])
        .range([0, innerWidth]);

    const y  = d3.scaleLinear()
        .domain([0, 10])
        .range([0, innerHeight]);

    // Define x and y domains
    // x.domain([1, d3.max(dataset_ratings, d => d.x)]);
    y.domain([10, 0]);

    // Add the x axis
    var ticks = dataset_ratings.length;
    if (ticks === 3) {
        ticks = 2;
    }

    g.append("g")
        .attr("transform", `translate(0,${innerHeight})`)
        .call(d3.axisBottom().scale(x).ticks(ticks));

    // Add the y axis
    g.append("g")
        .call(d3.axisLeft(y));

    // Create line generator
    const line = d3.line()
        .x(d => x(d.x))
        .y(d => y(d.y));

    // Add the lines to the SVG - Mean, Sentiment, Ratings
    g.append("path")
        .datum(dataset_mean)
        .attr("fill", "none")
        .attr("stroke", "red")
        .style("stroke-dasharray", ("8, 8"))
        .attr("stroke-width", 3)
        .attr("d", line);

    g.append("path")
        .datum(dataset_sentiment)
        .attr("fill", "none")
        .attr("stroke", "yellow")
        .style("stroke-dasharray", ("4, 8"))
        .attr("stroke-width", 2)
        .attr("d", line);

    g.append("path")
        .datum(dataset_ratings)
        .attr("fill", "none")
        .attr("stroke", "#18DB5C")
        .attr("stroke-width", 3)
        .attr("d", line);

    // Create Legend
    const keys = ["Ratings", "Mean", "Sentiment"];
    const colour = ["#18DB5C", "red", "yellow"];

    const legend_positions = [
        {'x': 80, 'y': 2},
        {'x': 80, 'y': 70},
        {'x': 2, 'y': 2},
        {'x': 2, 'y': 70},
    ];

    var pos;


    // Calculate best position for legend

    // FYI: I'm fully aware that this code is not good, can't think of a better way right now

    // It works though! :)
    var top_right = true;
    var bottom_right = true;
    var top_left = true;
    var bottom_left = true;

    var iterations = Math.ceil(dataset_ratings.length * 0.25);

    for (let i = 0; i < iterations; i++) {
        if (dataset_ratings[i]['y'] > 8) {
            top_left = false;
        }
        else if (dataset_ratings[i]['y'] < 3) {
            bottom_left = false;

        }

        var index = dataset_ratings.length - (i + 1);

        if (dataset_ratings[index]['y'] > 8) {
            top_right = false;
        }
        else if (dataset_ratings[index]['y'] < 3) {
            bottom_right = false;
        }
    }

    if (top_right && dataset_mean[0]['y'] < 8) {
        pos = legend_positions[0]
    }
    else if (bottom_right && dataset_mean[0]['y'] > 3) {
        pos = legend_positions[1]
    }
    else if (top_left && dataset_mean[0]['y'] < 8) {
        pos = legend_positions[2]
    }
    else {
        pos = legend_positions[3]
    }

    // Add one dot in the legend for each name.
    g.selectAll("mydots")
    .data(keys)
    .enter()
    .append("circle")
        .attr("cx", pos['x'] + "%")
        .attr("cy", function(d,i){ return pos['y'] + i*5 + "%"})
        .attr("r", 7)
        .style("fill", function(d,i){ return colour[i]})

    // Labels
    g.selectAll("mylabels")
    .data(keys)
    .enter()
    .append("text")
        .attr("x", (pos['x'] + 2) + "%")
        .attr("y", function(d,i){ return pos['y'] + i*5 + "%"})
        .style("fill", "white")
        .text(function(d){ return d})
        .attr("text-anchor", "left")
        .style("alignment-baseline", "middle")


    // Add the axis labels
    svg.append("text")
        .attr("class", "x label")
        .attr("text-anchor", "middle")
        .attr("x", width / 2)
        .attr("y", height)
        .style('font-size', '12px')
        .style('fill', 'white')
        .text("Track Number")

    svg.append("text")
        .attr("class", "y label")
        .attr("text-anchor", "middle")
        .attr("x", -height / 2)
        .attr("y", 10)
        .attr("transform", "rotate(-90)")
        .style('font-size', '12px')
        .style('fill', 'white')
        .text("Rating")
}


drawChart(window.lineChartData);
window.addEventListener("resize", drawChart(window.lineChartData));
