import { Line } from 'vue-chartjs'
import chartjsPluginAnnotation from "chartjs-plugin-annotation"

export default {
  extends: Line,
  props: ["chart", "resource"],
  mounted () {
    let timeout = 4000
    // prepare labels and datasets
    let self = this
    this.chart.datacollection.labels = []
    this.chart.datacollection.datasets = []

    this.chart.items.forEach((item) => {
      self.chart.datacollection.labels.push(
        item.created_at
      )
    });

    let n=0
    let plots = this.resource.data.chartPlots.split("\n")
    plots.forEach((plot) => {

      let data = []
      let dataTimeout = []

      this.chart.items.forEach((item) => {
        data.push(isNaN(item.data[0][plot])? timeout : item.data[0][plot] )
        dataTimeout.push(timeout)
      });

      this.chart.datacollection.datasets.push({
        borderColor: this.chart.datacollection.colors[n++],
        label: plot,
        fill: false,
        data: data
      });

      this.chart.datacollection.datasets.push({
        borderColor: "#DD3232",
        label: 'timeout',
        fill: false,
        data: dataTimeout
      });

    });

    // // data updated update chart (fresh reload)  CHECK
    // this.addPlugin([chartjsPluginAnnotation]),
    
    this.renderChart(this.chart.datacollection, this.chart.options)
  }
}