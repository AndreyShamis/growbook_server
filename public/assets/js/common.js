function floorDate(datetime) {
    var newDate = new Date(datetime);
    //newDate.setHours(0);
    newDate.setMonth(newDate.getMonth() -1);    // Fix issue https://github.com/google/google-visualization-issues/issues/1058
    // if (newDate.getMinutes() > 30 && newDate.getHours() < 23) {
    //     console.log(newDate.getHours());
    //     newDate.setHours(newDate.getHours() + 1);
    //     console.log(newDate.getHours());
    //     console.log('');
    // }
    newDate.setMinutes(0);
    newDate.setSeconds(0);
    return newDate;
}

function floorDateDaily(datetime) {
    var newDate = new Date(datetime);
    newDate.setMonth(newDate.getMonth() -1);    // Fix issue https://github.com/google/google-visualization-issues/issues/1058
    newDate.setHours(0);
    newDate.setMinutes(0);
    newDate.setSeconds(0);
    return newDate;
}

function CustomOnClickAction(e, legendItem) {
    var index = legendItem.datasetIndex;
    var ci = this.chart;
    var meta = ci.getDatasetMeta(index);
    // See controller.isDatasetVisible comment
    meta.hidden = meta.hidden === null ? !ci.data.datasets[index].hidden : null;
    // We hid a dataset ... rerender the chart
    ci.update();
}