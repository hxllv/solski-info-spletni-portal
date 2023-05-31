<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    headerNames: Array,
    data: Array,
    sortedAs: Array,
    allowEdit: Boolean,
    allowDelete: Boolean,
});



const pageNo = ref(false);
pageNo.value = 1

const noOf = computed(() => {
    return props.data.length
});

const noOfPages = computed(() => {
    return Math.ceil(props.data.length / 10)
});

// razbije data na delce po 10

const dataPaginated = computed(() => {
    const newData = {}
    let j = 1;
    for (let i = 0; i < props.data.length; i += 10) {
        const slice = props.data.slice(i, i + 10)
        const tempObj = {
            data: slice,
            numbering: {
                low: i + 1, 
                hi: i + slice.length
            }
        }
        newData[j] = tempObj
        j++
    }
    
    return newData
})

//tole je pretty bad, sam je zarad tega pagination code manj ogaben

const helperArrays = computed(() => {
    return {
        first5: [1, 2, 3, 4, 5],
        minus2toPlus2: [pageNo.value - 2, pageNo.value - 1, pageNo.value, pageNo.value + 1, pageNo.value + 2],
        last5: [noOfPages.value - 4, noOfPages.value - 3, noOfPages.value - 2, noOfPages.value - 1, noOfPages.value],
    }
});

// izbira classa glede na to a smo na trenutnem pageu

const classes = "relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300";

const getClass = (pageNoTemp) => {
    return pageNoTemp == pageNo.value ?
        `${classes} text-white bg-blue-500 cursor-default` : `${classes} cursor-pointer hover:bg-gray-50`
}

// disabla chevrone glede na kje smo

const isAt = (str) => {
    switch (str) {
        case 'start':
            return pageNo.value === 1 ? 'disabled text-gray-300' : 'cursor-pointer hover:bg-gray-50' 
        case 'end': 
            return pageNo.value == noOfPages.value ? 'disabled text-gray-300' : 'cursor-pointer hover:bg-gray-50' 
    }
}
</script>

<script>
  export default {
    emits: ['edit', 'delete']
  }
</script>

<template>
    <div class="overflow-x-auto md:grid md:grid-cols-3 sm:rounded-t-md">
        <table class="table-auto mt-0 md:col-span-3">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 border-r" v-for="name in headerNames">{{ name }}</th>
                    <th scope="col" class="px-6 py-3" v-if="allowEdit || allowDelete"></th>
                </tr>
            </thead>
            <tbody class="text-xs md:text-sm lg:text-base">
                <tr v-for="row in dataPaginated[pageNo].data" class="bg-white border-b hover:bg-gray-100">
                    <td class="px-6 py-4 border-r" v-for="propName in sortedAs">{{ row[propName] }}</td>
                    <td class="px-6 py-4 text-right" v-if="allowEdit || allowDelete">
                        <a class="cursor-pointer" @click="$emit('edit', row.id)" v-if="allowEdit">
                            <font-awesome-icon icon="fa-solid fa-pen" class="text-gray-700 px-2"/> 
                        </a>
                        <a class="cursor-pointer" @click="$emit('delete', row.id)" v-if="allowDelete">
                            <font-awesome-icon icon="fa-solid fa-trash-can" class="text-gray-700 px-2"/>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="flex items-center justify-between border-gray-200 bg-white px-4 py-3 sm:px-6 col-span-3  sm:rounded-b-md">
        <div class="flex flex-1 justify-between sm:hidden">
            <a @click="pageNo > 1 ? pageNo-- : ''" :class="isAt('start')">
                <span class="sr-only">Previous</span>
                <font-awesome-icon icon="fa-solid fa-chevron-left" class="p-1" />
            </a>
            <a @click="pageNo < noOfPages ? pageNo++ : ''" :class="isAt('end')">
                <span class="sr-only">Next</span>
                <font-awesome-icon icon="fa-solid fa-chevron-right" class="p-1" />
            </a>
        </div>
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
            <p class="text-sm text-gray-700">
                Showing
                <span class="font-medium">{{ dataPaginated[pageNo].numbering.low }}</span>
                to
                <span class="font-medium">{{ dataPaginated[pageNo].numbering.hi }}</span>
                of
                <span class="font-medium">{{noOf}}</span>
                results
            </p>
            </div>
            <div>
            <nav class="isolate inline-flex -space-x-px rounded-md" aria-label="Pagination">
                <a @click="pageNo = 1" :class="isAt('start')" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300">
                    <span class="sr-only">Previous</span>
                    <font-awesome-icon icon="fa-solid fa-chevron-left" class="py-1" />
                    <font-awesome-icon icon="fa-solid fa-chevron-left" class="py-1" />
                </a>
                <a @click="pageNo > 1 ? pageNo-- : ''" :class="isAt('start')" class="relative inline-flex items-center px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300">
                    <span class="sr-only">Previous</span>
                    <font-awesome-icon icon="fa-solid fa-chevron-left" class="p-1" />
                </a>

                <!-- ce mamo manj kot 6 strani -->
                <template v-if="noOfPages < 6" v-for="(value, propName) in dataPaginated">
                    <a @click="pageNo = propName" :class="getClass(propName)">{{ propName }}</a>
                </template>

                <!-- ce mamo vec -->
                <template v-if="noOfPages >= 6">
                    <template v-if="pageNo <= 3" v-for="val in helperArrays.first5">
                        <a @click="pageNo = val" :class="getClass(val)">{{ val }}</a>
                    </template>

                    <template v-if="pageNo > 3 && pageNo < noOfPages - 2" v-for="val in helperArrays.minus2toPlus2">
                        <a @click="pageNo = val" :class="getClass(val)">{{ val }}</a>
                    </template>

                    <template v-if="pageNo >= noOfPages - 2" v-for="val in helperArrays.last5">
                        <a @click="pageNo = val" :class="getClass(val)">{{ val }}</a>
                    </template>
                </template>
                <a @click="pageNo < noOfPages ? pageNo++ : ''" :class="isAt('end')" class="relative inline-flex items-center px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300">
                    <span class="sr-only">Next</span>
                    <font-awesome-icon icon="fa-solid fa-chevron-right" class="p-1" />
                </a>
                <a @click="pageNo = noOfPages" :class="isAt('end')" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300">
                    <span class="sr-only">Next</span>
                    <font-awesome-icon icon="fa-solid fa-chevron-right" class="py-1" />
                    <font-awesome-icon icon="fa-solid fa-chevron-right" class="py-1" />
                </a>
            </nav>
            </div>
        </div>
    </div>
</template>
