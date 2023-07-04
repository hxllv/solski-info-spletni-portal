<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';
import Timetable from '@/Components/Timetable.vue';

defineProps({
    permission: Array,
    timetableEntries: Array,
    subjects: Array,
    sClass: Object,
    hours: Object,
});
</script>

<template>
    <AppLayout title="Namizje" :permission="permission">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <Timetable :subjects="subjects" :classId="sClass ? sClass.id : null" :data="timetableEntries" :hours="hours" :allowEdit="false" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
