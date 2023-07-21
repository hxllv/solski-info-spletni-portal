<script setup>
import TeacherLayout from '@/Layouts/TeacherLayout.vue';
import Select from '@/Components/Select.vue'
import Gradebook from '@/Components/Gradebook.vue'

const props = defineProps({
    subjects: Array,
    grades: Array,
    students: Array,
    teachersSubjects: Array,
    user: String,
    classId: String,
    classes: Array,
    permission: Array,
});
</script>

<template>
    <TeacherLayout :permission="permission" :classes="classes" :classId="classId" v-slot="layout">
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <Gradebook 
                :grades="grades"
                :subjects="subjects"
                :allowAdd="true"
                :allowEdit="[]"
                :allowDelete="true"
            />
        </div>
    </TeacherLayout>
</template>