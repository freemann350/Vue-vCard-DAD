<script lang='ts'>
import { computed, defineComponent, ref, onMounted } from "vue"
import { DoughnutChart, useDoughnutChart } from "vue-chart-3"
import { Chart, ChartData, ChartOptions, registerables } from "chart.js"
import { useUserStore } from '../stores/user'
import axios from "axios"

Chart.register(...registerables);
export default defineComponent({
  name: "App",
  components: { DoughnutChart },
  setup() {
    const dataValues = ref([])
    const dataLabels = ref([])
    const toggleLegend = ref(true)
    const userStore = useUserStore()
    const numberOfTransactions = ref()
    const numberOfCategories = ref()

    const testData = computed<ChartData<"doughnut">>(() => ({
      labels: dataLabels.value,
      datasets: [
        {
          data: dataValues.value,
          backgroundColor: [
            "#a4fba6",
            "#4ae54a",
            "#30cb00",
            "#0f9200",
            "#006203",
          ],
        },
      ],
    }));

    const options = computed<ChartOptions<"doughnut">>(() => ({
      plugins: {
        legend: {
          position: toggleLegend.value ? "top" : "bottom",
        },
        title: {
          display: true,
          text: "Category Usage Chart",
        },
      },
    }));

    const { doughnutChartProps, doughnutChartRef } = useDoughnutChart({
      chartData: testData,
      options,
    });

    async function fetchData() {
      try {
        const response = await axios.get('transactions/vcard/' + userStore.userId + '/stats')
        const apiData = response.data

        const response2 = await axios.get('transactions/vcard/' + userStore.userId + '/totalNumActions')
        numberOfTransactions.value = response2.data

        const response3 = await axios.get('categories/' + userStore.userId + '/stats')
        numberOfCategories.value = response3.data
        
        const modifiedData = apiData.map((item: any) => ({
          name: item.name !== null ? item.name : "No Category",
          count: item.count,
        }))

        const categories = modifiedData.map((item: any) => item.name)
        const counts = modifiedData.map((item: any) => item.count)

        dataValues.value = counts
        dataLabels.value = categories

      } catch (error) {
        console.error("Error fetching data:", error)
      }
    }

    onMounted(() => {
      fetchData(); 
    })

    return {
      testData,
      options,
      doughnutChartRef,
      doughnutChartProps,
      numberOfTransactions,
      numberOfCategories,
    };
  },
});

</script>


<template>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
      <h1 class="h2">Dashboard</h1>
    </div>
    <div class="d-flex flex-wrap justify-content-between">
      <div class="w-50">
        <h3> Statistics</h3>
        <DoughnutChart v-bind="doughnutChartProps" />
      </div>
      <div class="w-50">
        <div class="container mt-5">
          <div class="row">
              <div class="col-md-6  mt-2">
                  <div class="card">
                      <div class="card-body">
                          <h5 class="card-title">Number of Transactions:</h5>
                          <p class="card-text">{{ numberOfTransactions }}</p>
                      </div>
                  </div>
              </div>
              <div class="col-md-6  mt-2">
                  <div class="card">
                      <div class="card-body">
                          <h5 class="card-title">Number of Personal Categories:</h5>
                          <p class="card-text">{{ numberOfCategories }}</p>
                      </div>
                  </div>
              </div>
          </div>
        </div>
      </div>
    </div>
    
</template>
  