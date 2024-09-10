<script setup>
import { ref, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'

const props = defineProps({
  user: Object,
  rival: Object
})

const loading = ref(true)
const chartstats = ref([])

const filter = ref({
  style: 'SP',
  difficulty: null,
  level: null,
  lamp: null,
  grade: null
})

const sort = ref('TITLE')
const reverse = ref(false)
const isPrivate = ref(false)

const loadChartstats = (force = false) => {
  if (props.rival && props.rival.scope !== 'public') {
    isPrivate.value = true
    return
  }
  loading.value = true
  fetch('/api/getscore' + (props.rival ? '/' + props.rival.name : ''), {
    method: 'GET',
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json'
    },
    cache: force ? 'no-cache' : 'force-cache'
  })
    .then((response) => response.json())
    .then((data) => {
      chartstats.value = data
    })
    .catch((error) => console.error('Error:', error))
    .finally(() => (loading.value = false))
}

onMounted(() => {
  loadChartstats()
})

const filterFn = (chartstat) => {
  return (
    chartstat.playtype === filter.value.style &&
    (filter.value.difficulty === null || chartstat.difficulty === filter.value.style + filter.value.difficulty) &&
    (filter.value.level === null || chartstat.level === filter.value.level) &&
    (filter.value.lamp === null || chartstat.lamp === filter.value.lamp) &&
    (filter.value.grade === null || chartstat.grade === filter.value.grade)
  )
}

const sortFn = (a, b) => {
  if (sort.value === 'TITLE') {
    if (a.title < b.title) {
      return reverse.value ? 1 : -1
    } else if (a.title > b.title) {
      return reverse.value ? -1 : 1
    } else {
      return 0
    }
  } else if (sort.value === 'DIFFICULTY') {
    return b.difficulty - a.difficulty
  } else if (sort.value === 'LEVEL') {
    return reverse.value ? b.level - a.level : a.level - b.level
  } else if (sort.value === 'LAMP') {
    const lamps = ['NP', 'AC', 'EC', 'NC', 'HC', 'EX', 'FC']
    return reverse.value ? lamps.indexOf(b.lamp) - lamps.indexOf(a.lamp) : lamps.indexOf(a.lamp) - lamps.indexOf(b.lamp)
  } else if (sort.value === 'SCORERATE') {
    return reverse.value ? b.percent_max - a.percent_max : a.percent_max - b.percent_max
  } else if (sort.value === 'MISS') {
    return reverse.value ? b.miss - a.miss : a.miss - b.miss
  }
}
</script>

<template>
  <AppLayout title="Score">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ props.rival ? props.rival.name + "'s" : 'My' }} Score
      </h2>
    </template>

    <div class="py-12 text-gray-800 dark:text-gray-200">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-2">
          <div>
            <span class="align-middle">STYLE</span>
            <span v-for="style in ['SP', 'DP']" class="inline-block ml-2 mb-2">
              <PrimaryButton v-if="filter.style === style">{{ style }}</PrimaryButton>
              <SecondaryButton v-else @click="filter.style = style">{{ style }}</SecondaryButton>
            </span>
          </div>
          <div>
            <span class="align-middle">DIFFICULTY</span>
            <span v-for="difficulty in ['B', 'N', 'H', 'A', 'L']" class="inline-block ml-2 mb-2">
              <PrimaryButton v-if="filter.difficulty === difficulty" @click="filter.difficulty = null">{{
                difficulty
              }}</PrimaryButton>
              <SecondaryButton v-else @click="filter.difficulty = difficulty">{{ difficulty }}</SecondaryButton>
            </span>
          </div>
          <div>
            <span class="align-middle">LEVEL</span>
            <span v-for="level in [...Array(12)].map((_, i) => i + 1)" class="inline-block ml-2 mb-2">
              <PrimaryButton v-if="filter.level === level" @click="filter.level = null">{{ level }}</PrimaryButton>
              <SecondaryButton v-else @click="filter.level = level">{{ level }}</SecondaryButton>
            </span>
          </div>
          <div>
            <span class="align-middle">LAMP</span>
            <span v-for="lamp in ['FC', 'EX', 'HC', 'NC', 'EC', 'AC', 'NP']" class="inline-block ml-2 mb-2">
              <PrimaryButton v-if="filter.lamp === lamp" @click="filter.lamp = null">{{ lamp }}</PrimaryButton>
              <SecondaryButton v-else @click="filter.lamp = lamp">{{ lamp }}</SecondaryButton>
            </span>
          </div>
          <div>
            <span class="align-middle">GRADE</span>
            <span v-for="grade in ['AAA', 'AA', 'A', 'B', 'C', 'D', 'E', 'F']" class="inline-block ml-2 mb-2">
              <PrimaryButton v-if="filter.grade === grade" @click="filter.grade = null">{{ grade }}</PrimaryButton>
              <SecondaryButton v-else @click="filter.grade = grade">{{ grade }}</SecondaryButton>
            </span>
          </div>
          <div>
            <span class="align-middle">SORT</span>
            <span
              v-for="s in ['TITLE', 'DIFFICULTY', 'LEVEL', 'LAMP', 'SCORERATE', 'MISS']"
              class="inline-block ml-2 mb-2"
            >
              <PrimaryButton v-if="sort === s" @click="reverse = !reverse">
                {{ s + ' ' + (reverse ? '▼' : '▲') }}
              </PrimaryButton>
              <!-- prettier-ignore -->
              <SecondaryButton v-else @click="sort = s; reverse = false">
                {{ s }}
              </SecondaryButton>
            </span>
          </div>
        </div>
        <div class="flex justify-between items-end mb-2">
          <div>Count : {{ chartstats.filter(filterFn).length }}</div>
          <div>
            <a
              :href="`https://x.com/intent/tweet?text=${encodeURI(
                route('user.name', props.rival ? props.rival.name : props.user.name)
              )}`"
              target="_blank"
              v-if="props.rival || props.user.scope === 'public'"
            >
              <SecondaryButton class="mr-2">Share</SecondaryButton>
            </a>
            <SecondaryButton @click="loadChartstats(true)">Reload</SecondaryButton>
          </div>
        </div>
        <div class="bg-white dark:bg-gray-800 overflow-x-scroll shadow-xl sm:rounded-lg">
          <p class="p-4" v-if="isPrivate">{{ props.rival.name }}'s Score is private.</p>
          <p class="p-4" v-else-if="loading">Loading score data...</p>
          <p class="p-4" v-else-if="chartstats.length === 0">Score is not uploaded.</p>
          <table v-else class="w-full text-center">
            <thead>
              <tr class="border-b-2 border-gray-300 dark:border-gray-600">
                <th class="px-2 py-1">TITLE</th>
                <th class="px-2 py-1">DIFF</th>
                <th class="px-2 py-1">LEVEL</th>
                <th class="px-2 py-1">LAMP</th>
                <th class="px-2 py-1">GRADE</th>
                <th class="px-2 py-1">SCORE</th>
                <th class="px-2 py-1">MISS</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="chartstat of chartstats.filter(filterFn).sort(sortFn)"
                class="border-b border-gray-300 dark:border-gray-600"
              >
                <td class="py-1 whitespace-pre-wrap">
                  {{
                    chartstat.title.replace(
                      / *(-[^-]+-|\(.+\)|~.+~|～.+～|feat\..+|ft\..+|With Money.*|そしてお米を.*|-JAKA.*)$/,
                      '\n$1'
                    )
                  }}
                </td>
                <td>
                  {{ chartstat.difficulty }}
                </td>
                <td>
                  {{ chartstat.level }}
                </td>
                <td>
                  {{ chartstat.lamp }}
                </td>
                <td>
                  {{ chartstat.grade }}
                </td>
                <td>
                  {{ chartstat.ex_score }}
                </td>
                <td>
                  {{ chartstat.miss === 9999 ? '-' : chartstat.miss }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
