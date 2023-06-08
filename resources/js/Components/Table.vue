<script setup>
import Spinner from '@/Components/Spinner.vue';
import TablePaginator from '@/Components/TablePaginator.vue';

const props = defineProps({
    headerNames: Array,
    data: Object,
    sortedAs: Array,
    allowEdit: Boolean,
    allowDelete: Boolean,
    query: Object,
    buffer: Boolean
});

defineEmits(['edit', 'delete']);
</script>

<template>
    <div class="relative shadow">
        <Spinner :buffer="buffer" />

        <div class="overflow-x-auto grid grid-cols-3 sm:rounded-t-md">
            <table class="table-auto mt-0 col-span-3">
                <thead class="text-xs text-left text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 border-r" v-for="name in headerNames">{{ name }}</th>
                        <th scope="col" class="px-6 py-3 max-w-max" v-if="allowEdit || allowDelete"></th>
                    </tr>
                </thead>
                <tbody class="text-xs md:text-sm lg:text-base">
                    <tr v-for="row in data.data" class="bg-white border-b hover:bg-gray-100">
                        <td class="px-6 py-4 border-r" v-for="val in sortedAs">{{ row[val] }}</td>
                        <td class="px-6 py-4 text-right max-w-max" v-if="allowEdit || allowDelete">
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

        <TablePaginator :query="query" :data="data" />
    </div>
</template>
