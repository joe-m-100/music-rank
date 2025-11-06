@php
    $album_length = $tracks->count();
@endphp

<script type="module">
    import * as d3 from "https://cdn.jsdelivr.net/npm/d3@7/+esm";

    // Declare the chart dimensions and margins.
    const width = 400;
    const height = 300;
    const marginTop = 20;
    const marginRight = 20;
    const marginBottom = 50;
    const marginLeft = 50;

    const data = {!! json_encode($line_chart_data) !!};

    // Declare the x (horizontal position) scale.
    const x = d3.scaleLinear()
        .domain([1, {{ $album_length }}])
        .range([marginLeft, width - marginRight]);

    // Declare the y (vertical position) scale.
    const y = d3.scaleLinear()
        .domain([0, 10])
        .range([height - marginBottom, marginTop]);

    // Create the SVG container.
    const svg = d3.create("svg")
        .attr("width", width)
        .attr("height", height);


    // Add the x-axis.
    svg.append("g")
        .attr("transform", `translate(0,${height - marginBottom})`)
        .call(d3.axisBottom(x).ticks({{ $album_length }}));

    // Add the y-axis.
    svg.append("g")
        .attr("transform", `translate(${marginLeft},0)`)
        .call(d3.axisLeft(y));

    // Add the axis labels
    svg.append("text")
        .attr("class", "x label")
        .attr("text-anchor", "middle")
        .attr("x", width / 2)
        .attr("y", height - 10)
        .style('font-size', '12px')
        .style('fill', 'white')
        .text("Track Number")

    svg.append("text")
        .attr("class", "y label")
        .attr("text-anchor", "middle")
        .attr("x", -height / 2)
        .attr("y", 20)
        .attr("transform", "rotate(-90)")
        .style('font-size', '12px')
        .style('fill', 'white')
        .text("Rating")

    // Create line generator
    const line = d3.line()
        .x(d => x(d.x))
        .y(d => y(d.y))

    svg.append("path")
        .datum(data)
        .attr("fill", "none")
        .attr("stroke", "#18DB5C")
        .attr("stroke-width", 3)
        .attr("d", line);


    // Append the SVG element.
    lineChart.append(svg.node());
</script>

<x-layout>
    <h1 class=" text-[34px] font-bold mb-10">{{ $heading }}</h1>

    <div class="w-[50%]">
        <div id="lineChart" class="w-full max-w-3xl aspect-[3/2] mx-auto"></div>
    </div>

</x-layout>
