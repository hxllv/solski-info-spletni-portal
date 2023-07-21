<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    grades: Array,
    subjects: Array,
    allowAdd: {
        type: Boolean,
        default: false,
    },
    allowEdit: {
        type: Array,
        default: [],
    },
    allowDelete: {
        type: Boolean,
        default: false,
    },
});

const subjectsWithGrades = computed(() => {
    let newSubjects = JSON.parse(JSON.stringify(props.subjects));

    newSubjects.forEach(subject => {
        subject.grades = []

        if (props.grades) {
            const tempGrades = props.grades.filter((grade) => subject.id == grade.id)[0];

            subject.grades = tempGrades.grades;
        }     
    })

    return newSubjects
});

console.log(subjectsWithGrades.value)
</script>

<template>
    <div class="relative shadow">
        <table class="table-fixed mt-0 col-span-3">
            <tbody class="text-xs md:text-sm lg:text-base">
                <tr v-for="subject in subjectsWithGrades" class="bg-white border-b hover:bg-gray-100">
                    <td class="p-3 border-r text-center text-gray-600">
                        <div>{{ subject.name }}</div>     
                    </td>
                    <td class="px-6 py-3 border-r">
                        <div v-for="grade in subject.grades">{{ grade.grade }}</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>