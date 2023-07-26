<script setup>
import TeacherLayout from '@/Layouts/TeacherLayout.vue';

const props = defineProps({
    classId: String,
    classes: Array,
    permission: Array,
});
</script>

<template>
    <TeacherLayout title="Učitelji" :permission="permission" :classes="classes" :classId="classId" v-slot="layout">
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <h1 class="mb-4 text-4xl font-extrabold leading-none tracking-tight text-gray-400 md:text-5xl lg:text-6xl text-center" v-if="!layout.classId">
                Prosimo izberite razred
            </h1>
        </div>
    </TeacherLayout>
</template>
