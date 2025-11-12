

function drawChart(data) {

    const container = d3.select("#barChartContainer")
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


    const dataset = data;

    // Create Tooltip
    const tooltip = d3.select("#barChartContainer")
        .append("div")
        .style("opacity", 0)
        .attr("class",
             "bg-white absolute text-black/75 text-[12px] p-[10px] rounded-lg pointer-events-none transition max-w-[20%]"
            );

    // Three function that change the tooltip when user hover / move / leave a cell
    const mouseover = function(event, d) {
        tooltip
            .html("<p class='text-black mb-1 font-semibold'>Rating: " + d.category + "</p>" + d.tracks)
            .style("opacity", 1)

    }

    const mousemove = function(event, d) {
        // console.log(event.offsetX, event.offsetY);
        tooltip
            .style("left",(event.pageX)+15+"px")
            .style("top",((event.pageY)-10)+"px")
    }

    const mouseleave = function(event, d) {
        tooltip
            .style("opacity", 0)
    }

    // Axis
    const x = d3.scaleBand()
        .domain(dataset.map(d => d.category))
        .range([0, innerWidth])
        .padding(0.1);

    const y = d3.scaleLinear()
        .domain([d3.max(dataset, d => d.value), 0])
        .range([0, innerHeight]);

    // Add x axis
    g.append("g")
        .attr("transform", `translate(0,${innerHeight})`)
        .call(d3.axisBottom().scale(x).ticks(10));

    // Add the y axis
    g.append("g")
        .call(d3.axisLeft(y))
        .call(d3.axisLeft().scale(y).ticks(d3.max(dataset, d => d.value)));

    // Add bars
    g.selectAll(".bar")
        .data(dataset)
        .enter()
        .append("rect")
        .attr("class", "fill-[#18DB5C] hover:fill-[#21FC6E] transition")
        .attr("x", d => x(d.category))
        .attr("y", d => y(d.value))
        .attr("width", x.bandwidth())
        .attr("height", d => innerHeight - y(d.value))
        .on("mouseover", mouseover)
        .on("mousemove", mousemove)
        .on("mouseleave", mouseleave);

    // Add the axis labels
    svg.append("text")
        .attr("class", "x label")
        .attr("text-anchor", "middle")
        .attr("x", width / 2)
        .attr("y", height - 5)
        .style('font-size', '12px')
        .style('fill', 'white')
        .text("Rating")

    svg.append("text")
        .attr("class", "y label")
        .attr("text-anchor", "middle")
        .attr("x", -height / 2)
        .attr("y", 10)
        .attr("transform", "rotate(-90)")
        .style('font-size', '12px')
        .style('fill', 'white')
        .text("Frequency")
}


drawChart(window.chartData);
window.addEventListener("resize", drawChart(window.chartData));
