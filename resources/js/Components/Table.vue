<script setup>
import Spinner from "@/Components/Spinner.vue";
import TablePaginator from "@/Components/TablePaginator.vue";
import Checkbox from "@/Components/Checkbox.vue";
import { computed, reactive, watch, ref } from "vue";
import { useForm, Link } from "@inertiajs/vue3";

const props = defineProps({
    headerNames: Array,
    data: Object,
    sortedAs: Array,
    allowEdit: Boolean,
    allowDelete: Boolean,
    allowMultiActions: Boolean,
    detailsURL: String,
    query: Object,
    buffer: Boolean,
    routeName: String,
    pageName: {
        type: String,
        default: "page",
    },
    routeParams: {
        type: Array,
        default: [],
    },
});

const emit = defineEmits(["edit", "delete", "selectedChange"]);

const selectAll = ref(false);

const idObj = reactive({});

props.data.data.forEach((dat) => {
    idObj[dat.id] = false;
});

watch(
    () => props.data,
    async (newData, oldData) => {
        Object.keys(idObj).forEach((key) => delete idObj[key]);
        newData.data.forEach((dat) => {
            idObj[dat.id] = false;
        });

        selectAll.value = false;
    }
);

watch(selectAll, async (newData, oldData) => {
    for (const key in idObj) {
        idObj[key] = false;
    }

    if (newData) {
        for (const key in idObj) {
            idObj[key] = true;
        }
    }

    selectedChange();
});

const selectedChange = () => {
    const asArray = Object.entries(idObj);
    const filtered = asArray
        .filter(([key, value]) => {
            return value;
        })
        .map(([key, value]) => {
            return key;
        });

    emit("selectedChange", filtered);
};
</script>

<template>
    <div class="relative">
        <Spinner :buffer="buffer" />

        <div class="overflow-x-auto grid grid-cols-3 sm:rounded-md shadow">
            <table class="table-fixed mt-0 col-span-3">
                <thead
                    class="text-xs text-left text-gray-700 uppercase bg-gray-50"
                >
                    <tr>
                        <th
                            scope="col"
                            class="py-3 border-r max-w-fit"
                            v-if="allowMultiActions"
                        >
                            <div class="flex justify-center px-1">
                                <Checkbox v-model:checked="selectAll" />
                            </div>
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-3 border-r"
                            v-for="name in headerNames"
                        >
                            {{ name }}
                        </th>
                        <th
                            scope="col"
                            class="py-3 max-w-fit"
                            v-if="allowEdit || allowDelete || detailsURL"
                        ></th>
                    </tr>
                </thead>
                <tbody class="text-xs md:text-sm lg:text-base">
                    <tr
                        v-for="row in data.data"
                        class="bg-white border-b hover:bg-gray-100"
                    >
                        <td class="border-r max-w-fit" v-if="allowMultiActions">
                            <div class="flex justify-center px-1">
                                <Checkbox
                                    v-model:checked="idObj[row.id]"
                                    @change="selectedChange"
                                />
                            </div>
                        </td>
                        <td class="px-6 py-4 border-r" v-for="val in sortedAs">
                            {{ row[val] }}
                        </td>
                        <td
                            class="text-right max-w-fit"
                            v-if="allowEdit || allowDelete || detailsURL"
                        >
                            <div class="flex justify-center px-1">
                                <Link
                                    :href="route(detailsURL, row.id)"
                                    v-if="detailsURL"
                                >
                                    <font-awesome-icon
                                        :icon="['fas', 'circle-info']"
                                        class="text-gray-700 px-2"
                                    />
                                </Link>
                                <a
                                    class="cursor-pointer"
                                    @click="$emit('edit', row.id)"
                                    v-if="allowEdit"
                                >
                                    <font-awesome-icon
                                        icon="fa-solid fa-pen"
                                        class="text-gray-700 px-2"
                                    />
                                </a>
                                <a
                                    class="cursor-pointer"
                                    @click="$emit('delete', row.id)"
                                    v-if="allowDelete"
                                >
                                    <font-awesome-icon
                                        icon="fa-solid fa-trash-can"
                                        class="text-gray-700 px-2"
                                    />
                                </a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <TablePaginator
                :query="query"
                :data="data"
                :pageName="pageName"
                :routeName="routeName"
                :routeParams="routeParams"
            />
        </div>
    </div>
</template>
