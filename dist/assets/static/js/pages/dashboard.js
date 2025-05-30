// Fungsi untuk mengambil data pesanan bulanan dari server
async function fetchMonthlyOrders() {
  try {
    let response = await fetch('/Smurfer/admin/monthlyOrder.php'); 
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    let data = await response.json();
    return data;
  } catch (error) {
    console.error('Error fetching monthly orders:', error);
    return [];
  }
}

let optionsProfileVisit = {
  annotations: {
    position: 'back',
  },
  dataLabels: {
    enabled: false,
  },
  chart: {
    type: 'bar',
    height: 300,
  },
  fill: {
    opacity: 1,
  },
  plotOptions: {},
  series: [
    {
      name: 'Order',
      data: [], 
    },
  ],
  colors: '#435ebe',
  xaxis: {
    categories: [
      'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
      'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',
    ],
  },
};

async function renderProfileVisitChart() {
  const monthlyOrders = await fetchMonthlyOrders();

  optionsProfileVisit.series[0].data = monthlyOrders;

  var chartProfileVisit = new ApexCharts(
    document.querySelector('#chart-profile-visit'),
    optionsProfileVisit
  );

  chartProfileVisit.render();
}

// Panggil fungsi untuk merender chart
renderProfileVisitChart();

// Inisialisasi chart lain dengan data statis
let optionsVisitorsProfile = {
  series: [70, 30],
  labels: ['Male', 'Female'],
  colors: ['#435ebe', '#55c6e8'],
  chart: {
    type: 'donut',
    width: '100%',
    height: '350px',
  },
  legend: {
    position: 'bottom',
  },
  plotOptions: {
    pie: {
      donut: {
        size: '30%',
      },
    },
  },
};

var optionsEurope = {
  series: [
    {
      name: 'series1',
      data: [310, 800, 600, 430, 540, 340, 605, 805, 430, 540, 340, 605],
    },
  ],
  chart: {
    height: 80,
    type: 'area',
    toolbar: {
      show: false,
    },
  },
  colors: ['#5350e9'],
  stroke: {
    width: 2,
  },
  grid: {
    show: false,
  },
  dataLabels: {
    enabled: false,
  },
  xaxis: {
    type: 'datetime',
    categories: [
      '2018-09-19T00:00:00.000Z', '2018-09-19T01:30:00.000Z',
      '2018-09-19T02:30:00.000Z', '2018-09-19T03:30:00.000Z',
      '2018-09-19T04:30:00.000Z', '2018-09-19T05:30:00.000Z',
      '2018-09-19T06:30:00.000Z', '2018-09-19T07:30:00.000Z',
      '2018-09-19T08:30:00.000Z', '2018-09-19T09:30:00.000Z',
      '2018-09-19T10:30:00.000Z', '2018-09-19T11:30:00.000Z',
    ],
    axisBorder: {
      show: false,
    },
    axisTicks: {
      show: false,
    },
    labels: {
      show: false,
    },
  },
  yaxis: {
    labels: {
      show: false,
    },
  },
  tooltip: {
    x: {
      format: 'dd/MM/yy HH:mm',
    },
  },
};

let optionsAmerica = {
  ...optionsEurope,
  colors: ['#008b75'],
};

let optionsIndonesia = {
  ...optionsEurope,
  colors: ['#dc3545'],
};

var chartVisitorsProfile = new ApexCharts(
  document.getElementById('chart-visitors-profile'),
  optionsVisitorsProfile
);
var chartEurope = new ApexCharts(
  document.querySelector('#chart-europe'),
  optionsEurope
);
var chartAmerica = new ApexCharts(
  document.querySelector('#chart-america'),
  optionsAmerica
);
var chartIndonesia = new ApexCharts(
  document.querySelector('#chart-indonesia'),
  optionsIndonesia
);

chartIndonesia.render();
chartAmerica.render();
chartEurope.render();
chartVisitorsProfile.render();
