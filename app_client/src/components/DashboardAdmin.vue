<script lang='ts'>
import { computed, defineComponent, ref, onMounted } from "vue";
import { PieChart, BarChart, usePieChart, useBarChart } from "vue-chart-3";
import { Chart, ChartData, ChartOptions, registerables } from "chart.js";
import { useUserStore } from '../stores/user';
import axios from "axios";

Chart.register(...registerables);

export default defineComponent({
  name: "App",
  components: { PieChart, BarChart },
  setup() {
    const dataValues = ref([])
    const dataLabels = ref([])
    const barDataValues = ref([])
    const barDataLabels = ref([])
    const toggleLegend = ref(true)
    const userStore = useUserStore()

    const numberOfVCards = ref()
    const numberOfAdmins = ref()

    const numberOfCreditTransactions = ref()
    const numberOfDebitTransactions = ref()

    const barTestData = computed<ChartData<"bar">>(() => ({
      labels: barDataLabels.value,
      datasets: [
        {
          label: "vCard Status",
          data: barDataValues.value,
          backgroundColor: [
            "#84D08E",
            "#CB2C31",
          ],
        },
      ],
    }));

    const barOptions = computed<ChartOptions<"bar">>(() => ({
      plugins: {
        title: {
          display: true,
          text: "vCard Status",
        },
      },
    }));

    const testData = computed<ChartData<"pie">>(() => ({
      labels: dataLabels.value,
      datasets: [
        {
          data: dataValues.value,
          backgroundColor: [
            "#84D08E",
            "#3aaee2",
          ],
        },
      ],
    }));

    const options = computed<ChartOptions<"pie">>(() => ({
      plugins: {
        legend: {
          position: toggleLegend.value ? "top" : "bottom",
        },
        title: {
          display: true,
          text: "User Types Chart",
        },
      },
    }));

    const { pieChartProps, pieChartRef } = usePieChart({
      chartData: testData,
      options,
    });

    const { barChartProps, barChartRef } = useBarChart({
      chartData: barTestData,
      options: barOptions,
    });

    async function fetchData() {
      try {
        const response = await axios.get('vcards/stat')
        numberOfVCards.value = response.data

        const response2 = await axios.get('admins/stat')
        numberOfAdmins.value = response2.data

        dataValues.value = [numberOfVCards.value, numberOfAdmins.value]
        dataLabels.value = ['vCards', 'Admins']

        const response3 = await axios.get('transactions/statcredit')
        numberOfCreditTransactions.value = response3.data

        const response4 = await axios.get('transactions/statdebit')
        numberOfDebitTransactions.value = response4.data

      } catch (error) {
        console.error("Error fetching data:", error);
      }
    }

    async function fetchBarData() {
      try {
        const response = await axios.get('vcards/statactive')
        const numberOfActiveVCards = response.data

        const response2 = await axios.get('vcards/statblocked')
        const numberOfBlockedVCards = response2.data

        barDataValues.value = [numberOfActiveVCards, numberOfBlockedVCards]
        barDataLabels.value = ['Active', 'Blocked']


      } catch (error) {
        console.error("Error fetching bar chart data:", error);
      }
    }

    onMounted(() => {
      fetchData()
      fetchBarData()
    });

    return {
      testData,
      options,
      pieChartRef,
      pieChartProps,
      barChartProps,
      barChartRef,
      numberOfCreditTransactions,
      numberOfDebitTransactions,
    };
  },
});


</script>


<template>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
      <h1 class="h2">Dashboard Admin</h1>
    </div>
    <div class="d-flex flex-wrap justify-content-between">
      <div class="w-50">
        <h3> Statistics</h3>
        <PieChart v-bind="pieChartProps" />
      </div>
      <div class="w-50">
        <div class="container mt-5">
          <div class="row">
              <div class="col-md-6  mt-2">
                  <div class="card">
                      <div class="card-body">
                          <h5 class="card-title">Number Credit of Transactions:</h5>
                          <p class="card-text">{{ numberOfCreditTransactions }}</p>
                      </div>
                  </div>
              </div>
              <div class="col-md-6  mt-2">
                  <div class="card">
                      <div class="card-body">
                          <h5 class="card-title">Number Debit of Transactions:</h5>
                          <p class="card-text">{{ numberOfDebitTransactions }}</p>
                      </div>
                  </div>
              </div>
          </div>
        </div>
      </div>
      <div class="container mt-5">
        <div class="row">
          <div class="col-md-12 mt-2">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">vCard Status</h5>
                <BarChart v-bind="barChartProps" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
</template>
  