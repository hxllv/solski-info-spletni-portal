<script setup>
import TeacherLayout from "@/Layouts/TeacherLayout.vue";
import Select from "@/Components/Select.vue";
import Gradebook from "@/Components/Gradebook.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { Link, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps({
    subjects: Array,
    grades: Array,
    students: Array,
    teachersSubjects: Array,
    userId: String,
    classId: String,
    classes: Array,
    permission: Array,
});

const userIdVal = ref(props.userId);

watch(userIdVal, (newUser) => {
    router.get(
        route("gradebook"),
        {
            userId: newUser,
            classId: props.classId,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
});
</script>

<template>
    <TeacherLayout
        title="Redovalnica"
        :permission="permission"
        :classes="classes"
        :classId="classId"
        v-slot="layout"
    >
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="py-5 m-2 max-w-7xl mt-0 sm:px-6 lg:px-8 mx-auto">
                <InputLabel for="user" value="Učenec" />
                <Select
                    id="user"
                    v-model="userIdVal"
                    class="mt-1 block w-full"
                    autocomplete="role"
                >
                    <option v-for="student in students" :value="student.id">
                        {{ student.full_name }}
                    </option>
                </Select>
            </div>

            <Gradebook
                :grades="grades"
                :subjects="subjects"
                :userId="userId"
                :allowAdd="permission.includes('gradebook.create')"
                :allowEdit="permission.includes('gradebook.edit')"
                :allowDelete="permission.includes('gradebook.delete')"
                :allowActionsFor="teachersSubjects"
                :buffer="layout.buffer"
            />
        </div>
    </TeacherLayout>
</template>
