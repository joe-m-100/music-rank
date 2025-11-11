
function calculateIdealLegendPosition(ratings) {
    const positions = [
        {
            position: {'x': 80, 'y': 2}, // Top right
            rect: [
                [ 0.75 * ratings.length, 9 ],
                [ ratings.length, 10 ],
            ],
        },
        {
            position: {'x': 80, 'y': 70}, // Bottom right
            rect: [
                [ 0.75 * ratings.length, 1 ],
                [ ratings.length, 3 ],
            ],
        },
        {
            position: {'x': 2, 'y': 2}, // Top Left
            rect: [
                [ 1, 9 ],
                [ 0.25 * ratings.length, 10 ],
            ],
        },
        {
            position: {'x': 2, 'y': 70}, // Bottom Left
            rect: [
                [ 1, 1 ],
                [ 0.25 * ratings.length, 3 ],
            ],
        },
    ];

    const valid = positions.filter(position => {
        console.log("RESET");
        return ratings.every(rating => {
            const x = rating.x;

            const rating_doesnt_intersect = ! (
                (x >= position.rect[0][0] && position.rect[1][0] >= x)
                &&
                (position.rect[0][1] <= rating.y && rating.y <= position.rect[1][1])
            );

            // console.log(position.rect[0][0] + ' < ' + x + ' < ' + position.rect[1][0], (x >= position.rect[0][0] && position.rect[1][0] >= x));
            // console.log(position.rect[0][1] + ' < ' + rating.y + ' < ' + position.rect[1][1], (position.rect[0][1] <= rating.y && rating.y <= position.rect[1][1]));
            // console.log(!rating_doesnt_intersect);

            return rating_doesnt_intersect;
        });
    });

    console.log(valid);
    return valid[0]?.position || positions[0].position;
}


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

    const pos = calculateIdealLegendPosition(dataset_ratings);

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
