<script setup>
import TeacherLayout from "@/Layouts/TeacherLayout.vue";
import Select from "@/Components/Select.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import DateInput from "@/Components/DateInput.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import Table from "@/Components/Table.vue";
import Absences from "@/Components/Absences.vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps({
    availableAbsences: Array,
    absences: Object,
    students: Array,
    studentsPaginated: Object,
    teachersSubjects: Array,
    userId: String,
    classId: String,
    classTeacherClassIds: Array,
    forDate: String,
    hourAndSubject: Array,
    classes: Array,
    permission: Array,
    params: Object,
});

const userIdVal = ref(props.userId);
const forDateVal = ref(props.forDate);
const hourAndSubject = ref(props.hourAndSubject);

const addingAbsences = ref(false);
const selectedUsersAdding = ref(false);
const multiActionsAddingClass = ref("opacity-30 pointer-events-none");

const formAdd = useForm({});
const formFilterAdding = useForm({
    term: "",
});

formFilterAdding.term = props.params.term;

watch(userIdVal, (newUser) => {
    router.get(
        route("absences"),
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

watch(forDateVal, (newDate) => {
    hourAndSubject.value = "";

    router.get(
        route("absences"),
        {
            userId: userIdVal.value,
            classId: props.classId,
            forDate: newDate,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
});

watch(hourAndSubject, (newHour) => {
    if (!newHour) return;

    router.get(
        route("absences"),
        {
            userId: userIdVal.value,
            classId: props.classId,
            forDate: forDateVal.value,
            hourAndSubject: newHour,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
});

// adding

const onAddingSelectChange = (val) => {
    selectedUsersAdding.value =
        val.length > 0 && hourAndSubject.value ? val : false;

    multiActionsAddingClass.value = "";
    if (!selectedUsersAdding.value)
        multiActionsAddingClass.value = "opacity-30 pointer-events-none";
};

const closeAddingModal = () => {
    addingAbsences.value = false;
    selectedUsersAdding.value = false;
    hourAndSubject.value = "";
    multiActionsAddingClass.value = "opacity-30 pointer-events-none";
    forDateVal.value = new Date().toISOString().split("T")[0];

    formFilterAdding.reset();

    router.get(
        route("absences"),
        {
            userId: userIdVal.value,
            classId: props.classId,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

const openAddingModal = () => {
    addingAbsences.value = true;
};

const addAbsences = () => {
    const tempData = JSON.parse(hourAndSubject.value);

    formAdd
        .transform(() => ({
            userIds: selectedUsersAdding.value,
            hour: tempData[0],
            subject: tempData[1],
            date: forDateVal.value,
        }))
        .post(route("create.absences"), {
            preserveScroll: true,
            onSuccess: () => closeAddingModal(),
            onError: () => {},
        });
};

console.log(props);
</script>

<template>
    <TeacherLayout
        title="Izostanki"
        :permission="permission"
        :classes="classes"
        :classId="classId"
        v-slot="layout"
    >
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div
                class="md:grid md:grid-cols-4 md:gap-2"
                v-if="permission.includes('absences.create')"
            >
                <div class="mt-5 md:mt-0 md:col-span-1">
                    <PrimaryButton @click="openAddingModal">
                        Dodaj izostanek
                    </PrimaryButton>
                </div>
            </div>

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

            <Absences
                :absences="absences"
                :userId="userId"
                :classId="classId"
                :allowEdit="
                    permission.includes('absences.edit') &&
                    (classTeacherClassIds.includes(Number(classId)) ||
                        permission.includes('absences.bypass.class'))
                "
                :allowDelete="permission.includes('absences.delete')"
                :allowActionsFor="teachersSubjects"
                :allowApproval="permission.includes('absences.approval')"
                :isClassTeacher="
                    classTeacherClassIds.includes(Number(classId)) ||
                    permission.includes('absences.bypass.class')
                "
                :buffer="layout.buffer"
            />
        </div>

        <DialogModal
            :show="addingAbsences"
            @close="closeAddingModal"
            maxWidth="4xl"
        >
            <template #title> Dodaj izostanke </template>
            <template #content>
                <form @submit.prevent="">
                    <div class="px-4 py-5 bg-white sm:p-6">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-4">
                                <InputLabel for="date" value="Datum" />
                                <DateInput
                                    id="date"
                                    class="mt-1 block w-full"
                                    v-model="forDateVal"
                                />
                            </div>
                            <!-- Hour -->
                            <div class="col-span-6 sm:col-span-4">
                                <InputLabel for="hour" value="Ura" />
                                <Select
                                    id="hour"
                                    v-model="hourAndSubject"
                                    class="mt-1 block w-full"
                                >
                                    <option
                                        v-for="avail in availableAbsences"
                                        :value="
                                            JSON.stringify([
                                                avail.hour,
                                                avail.override
                                                    ? avail.override.id
                                                    : avail.subject_teacher_id,
                                            ])
                                        "
                                    >
                                        {{
                                            avail.override
                                                ? avail.override.name
                                                : avail.subject_teacher.name
                                        }}
                                        -
                                        {{
                                            avail.override
                                                ? avail.override.users_full_name
                                                : avail.subject_teacher
                                                      .users_full_name
                                        }}
                                        -
                                        {{ avail.hour_name }}
                                    </option>
                                </Select>
                                <InputError
                                    :message="formAdd.errors.hour"
                                    class="mt-2"
                                />
                            </div>
                        </div>
                    </div>
                </form>
                <div class="md:grid md:grid-cols-2 md:gap-6">
                    <div class="mt-5 md:mt-0 md:col-span-1 p-2">
                        <InputLabel for="term" value="Iskalni niz" />
                        <TextInput
                            id="term"
                            v-model="formFilterAdding.term"
                            type="text"
                            class="mt-1 block w-full"
                            autocomplete="term"
                        />
                    </div>
                    <div
                        class="mt-5 md:mt-0 md:col-span-1 p-2 text-right flex items-end justify-end"
                    >
                        <Link
                            preserve-scroll
                            preserve-state
                            :href="route('absences')"
                            :data="{
                                page: 1,
                                term: '',
                                userId: userIdVal,
                                classId: classId,
                                forDate: forDateVal,
                            }"
                            class="mr-1"
                            @click="formFilterAdding.reset()"
                        >
                            <SecondaryButton> Ponastavi </SecondaryButton>
                        </Link>

                        <Link
                            preserve-scroll
                            preserve-state
                            :href="route('absences')"
                            :data="{
                                page: 1,
                                term: formFilterAdding.term,
                                userId: userIdVal,
                                classId: classId,
                                forDate: forDateVal,
                            }"
                        >
                            <PrimaryButton> Uporabi </PrimaryButton>
                        </Link>
                    </div>
                </div>
                <Table
                    :data="studentsPaginated"
                    :headerNames="['Ime', 'Email']"
                    :sortedAs="['full_name', 'email']"
                    :allowEdit="false"
                    :allowDelete="false"
                    :allowMultiActions="true"
                    :query="{
                        term: formFilterAdding.term,
                        userId: userIdVal,
                        classId: classId,
                        forDate: forDateVal,
                    }"
                    :buffer="layout.buffer"
                    @selectedChange="onAddingSelectChange"
                    routeName="absences"
                />
            </template>

            <template #footer>
                <InputError :message="formAdd.errors.extra" class="mt-2" />
                <SecondaryButton @click="closeAddingModal">
                    Prekliči
                </SecondaryButton>

                <PrimaryButton
                    class="ml-3"
                    :class="multiActionsAddingClass"
                    @click="addAbsences"
                >
                    Shrani
                </PrimaryButton>
            </template>
        </DialogModal>
    </TeacherLayout>
</template>
