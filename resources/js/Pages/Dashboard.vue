<script setup>
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import Timetable from "@/Components/Timetable.vue";
import Absences from "@/Components/Absences.vue";
import Gradebook from "@/Components/Gradebook.vue";
import Tests from "@/Components/Tests.vue";

const props = defineProps({
    permission: Array,
    timetableEntries: Array,
    timetableOverrides: Array,
    absences: Object,
    grades: Object,
    tests: Object,
    subjects: Object,
    sClass: Object,
    users: Array,
    hours: Object,
    userId: String,
});

console.log(props);
</script>

<template>
    <DashboardLayout
        title="Namizje"
        :permission="permission"
        :users="users"
        :userId="userId"
        v-slot="layout"
    >
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="pb-5"
                    v-if="permission.includes('dashboard.timetable.view')"
                >
                    <div class="py-4 font-medium">Urnik</div>
                    <Timetable
                        :classId="sClass ? sClass.id : null"
                        :data="timetableEntries"
                        :overrides="timetableOverrides"
                        :hours="hours"
                        :allowEdit="false"
                        :buffer="layout.buffer"
                    />
                </div>
                <div
                    class="pb-5"
                    v-if="
                        permission.includes('dashboard.gradebook.view') &&
                        userId
                    "
                >
                    <div class="py-4 font-medium">Redovalnica</div>
                    <Gradebook
                        :grades="grades"
                        :subjects="subjects"
                        :userId="userId"
                        :buffer="layout.buffer"
                    />
                </div>
                <div
                    class="pb-5"
                    v-if="
                        permission.includes('dashboard.absences.view') && userId
                    "
                >
                    <div class="py-4 font-medium">Izostanki</div>
                    <Absences
                        :absences="absences"
                        :allowEdit="
                            permission.includes('dashboard.absences.edit')
                        "
                        :userId="userId"
                        :classId="sClass ? String(sClass.id) : null"
                        :buffer="layout.buffer"
                        routeName="dashboard.excuse"
                    />
                </div>
                <div
                    class="pb-5"
                    v-if="permission.includes('dashboard.tests.view') && userId"
                >
                    <div class="py-4 font-medium">Prihodnja ocenjevanja</div>
                    <Tests
                        :tests="tests"
                        :allowEdit="permission.includes('tests.edit')"
                        :buffer="layout.buffer"
                    />
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
