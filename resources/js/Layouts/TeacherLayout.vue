<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Tab from "@/Components/Tab.vue";
import Select from "@/Components/Select.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { Link, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps({
    title: String,
    backButtonURL: String,
    permission: Array,
    classes: Array,
    classId: String,
});

const classId = ref(props.classId);

const buffer = ref(false);
buffer.value = false;

router.on("start", () => {
    buffer.value = true;
});

router.on("finish", () => {
    buffer.value = false;
});

watch(classId, (newId) => {
    router.get(
        route().current(),
        {
            classId: newId,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
});
</script>

<template>
    <AppLayout :title="title" :permission="permission">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ title }}
            </h2>
        </template>

        <ul
            class="m-2 max-w-7xl mt-0 px-2 sm:px-6 lg:px-8 mx-auto flex flex-wrap text-sm font-medium text-center text-gray-500"
            v-if="!backButtonURL && classId"
        >
            <li class="mr-2" v-if="permission.includes('gradebook.view')">
                <Tab
                    :href="route('gradebook', { classId: classId })"
                    :active="route().current('gradebook')"
                >
                    Redovalnica
                </Tab>
            </li>
            <li class="mr-2" v-if="permission.includes('absences.view')">
                <Tab
                    :href="route('absences', { classId: classId })"
                    :active="route().current('absences')"
                >
                    Izostanki
                </Tab>
            </li>
            <li class="mr-2" v-if="permission.includes('tests.view')">
                <Tab
                    :href="route('tests', { classId: classId })"
                    :active="route().current('tests')"
                >
                    Ocenjevanja
                </Tab>
            </li>
            <li
                class="mr-2"
                v-if="permission.includes('timetable.override.view')"
            >
                <Tab
                    :href="route('overrides', { classId: classId })"
                    :active="route().current('overrides')"
                >
                    Nadomeščanja
                </Tab>
            </li>
        </ul>

        <div class="m-2 max-w-7xl mt-0 sm:px-6 lg:px-8 mx-auto flex flex-wrap">
            <Link
                class="p-2 pt-4 scale-150"
                :href="backButtonURL"
                v-if="backButtonURL"
            >
                <font-awesome-icon :icon="['fas', 'arrow-left']" />
            </Link>
        </div>

        <div class="py-5 m-2 max-w-7xl mt-0 sm:px-6 lg:px-8 mx-auto">
            <InputLabel for="class" value="Razred" />
            <Select id="class" v-model="classId" class="mt-1 block w-full">
                <option v-for="sClass in classes" :value="sClass.id">
                    {{ sClass.name }}
                </option>
            </Select>
        </div>

        <div>
            <slot :buffer="buffer" :classId="classId" />
        </div>
    </AppLayout>
</template>
