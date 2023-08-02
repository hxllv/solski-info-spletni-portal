<script setup>
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import Spinner from "@/Components/Spinner.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import Select from "@/Components/Select.vue";

const props = defineProps({
    grades: Array,
    subjects: Array,
    userId: String,
    allowAdd: {
        type: Boolean,
        default: false,
    },
    allowEdit: {
        type: Boolean,
        default: false,
    },
    allowDelete: {
        type: Boolean,
        default: false,
    },
    allowActionsFor: {
        type: Array,
        default: [],
    },
    buffer: Boolean,
});

const addingGrade = ref(false);
const addingGradeFor = ref(false);

const gradeInfo = ref(false);
const selectedGrade = ref(false);

const confirmingGradeDeletion = ref(false);
const gradeEditing = ref(false);

const formAdd = useForm({
    grade: "",
    note: "",
});
const formDelete = useForm({});
const formEdit = useForm({
    grade: "",
    note: "",
});

// adding

const closeAddingModal = () => {
    addingGrade.value = false;
    addingGradeFor.value = false;
    formAdd.reset();
};

const openAddingModal = (id) => {
    addingGrade.value = true;
    addingGradeFor.value = id;
};

const addGrade = () => {
    formAdd
        .transform(() => ({
            ...formAdd,
            subject: addingGradeFor.value,
            user: props.userId,
        }))
        .post(route("create.gradebook"), {
            preserveScroll: true,
            onSuccess: () => closeAddingModal(),
            onError: () => {},
        });
};

// info

const closeInfoModal = () => {
    gradeInfo.value = false;
    selectedGrade.value = false;
};

const openInfoModal = (grade) => {
    gradeInfo.value = true;
    selectedGrade.value = grade;
};

// deleting

const closeDeleteModal = (fullClose = false) => {
    gradeInfo.value = true;
    confirmingGradeDeletion.value = false;

    if (fullClose) closeInfoModal();
};

const openDeleteModal = () => {
    gradeInfo.value = false;
    confirmingGradeDeletion.value = true;
};

const deleteGrade = () => {
    formDelete.delete(route("gradebook.delete", selectedGrade.value.id), {
        preserveScroll: true,
        onSuccess: () => closeDeleteModal(true),
        onError: () => {},
    });
};

// editing

const closeEditModal = (fullClose = false) => {
    gradeInfo.value = true;
    gradeEditing.value = false;

    formEdit.reset();

    if (fullClose) closeInfoModal();
};

const openEditModal = () => {
    gradeInfo.value = false;
    gradeEditing.value = true;

    formEdit.grade = String(selectedGrade.value.grade);
    formEdit.note = selectedGrade.value.note;
};

const editGrade = () => {
    formEdit.put(route("gradebook.update", selectedGrade.value.id), {
        preserveScroll: true,
        onSuccess: () => closeEditModal(true),
    });
};

// mods

const subjectsWithGrades = computed(() => {
    let newSubjects = JSON.parse(JSON.stringify(props.subjects));

    if (!newSubjects) return;

    newSubjects.forEach((subject) => {
        subject.grades = [];

        if (props.grades) {
            const tempGrades = props.grades.filter(
                (grade) => subject.id == grade.id
            );

            if (tempGrades && tempGrades.length)
                subject.grades = tempGrades[0].grades;
        }
    });

    return newSubjects;
});
</script>

<template>
    <div class="relative">
        <Spinner :buffer="buffer" />

        <div class="overflow-x-auto grid grid-cols-3 sm:rounded-md shadow">
            <table class="table-fixed mt-0 col-span-3">
                <tbody class="text-xs md:text-sm lg:text-base">
                    <tr
                        v-for="subject in subjectsWithGrades"
                        class="bg-white border-b hover:bg-gray-100 grid grid-cols-4 break-words"
                    >
                        <td class="p-3 border-r text-center col-span-1">
                            <div>{{ subject.name }}</div>
                            <div class="text-xs md:text-sm text-gray-600">
                                {{ subject.user.full_name }}
                            </div>
                        </td>
                        <td class="px-6 py-3 border-r col-span-3">
                            <span
                                v-for="grade in subject.grades"
                                class="pr-2 cursor-pointer"
                                @click="openInfoModal(grade)"
                                >{{ grade.grade }}</span
                            >
                            <SecondaryButton
                                v-if="
                                    userId &&
                                    allowAdd &&
                                    allowActionsFor.includes(subject.id)
                                "
                                @click="openAddingModal(subject.id)"
                                class="ml-5"
                            >
                                Dodaj
                            </SecondaryButton>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <DialogModal :show="addingGrade" @close="closeAddingModal">
        <template #title> Dodaj oceno </template>

        <template #content>
            <form @submit.prevent="">
                <div class="px-4 py-5 bg-white sm:p-6">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- Grade -->
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="grade" value="Ocena" />
                            <Select
                                id="grade"
                                v-model="formAdd.grade"
                                class="mt-1 block w-full"
                            >
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </Select>

                            <InputError
                                :message="formAdd.errors.grade"
                                class="mt-2"
                            />
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="note" value="Opombe" />
                            <TextInput
                                id="note"
                                v-model="formAdd.note"
                                type="text"
                                class="mt-1 block w-full"
                            />
                            <InputError
                                :message="formAdd.errors.note"
                                class="mt-2"
                            />
                            <InputError
                                :message="formAdd.errors.user"
                                class="mt-2"
                            />
                            <InputError
                                :message="formAdd.errors.subject"
                                class="mt-2"
                            />
                        </div>
                    </div>
                </div>
            </form>
        </template>

        <template #footer>
            <SecondaryButton @click="closeAddingModal()">
                Prekliči
            </SecondaryButton>

            <PrimaryButton class="ml-3" @click="addGrade">
                Shrani
            </PrimaryButton>
        </template>
    </DialogModal>

    <DialogModal :show="gradeInfo" @close="closeInfoModal">
        <template #content>
            <ul class="list-disc py-2 pl-4">
                <li>Ocena: {{ selectedGrade.grade }}</li>
                <li v-if="selectedGrade.note">
                    Opombe: {{ selectedGrade.note }}
                </li>
                <li>
                    Datum vnosa:
                    {{
                        new Intl.DateTimeFormat("sl-SI", {
                            dateStyle: "long",
                            timeStyle: "long",
                        }).format(new Date(selectedGrade.created_at))
                    }}
                </li>
            </ul>
        </template>

        <template #footer>
            <DangerButton
                v-if="
                    allowDelete &&
                    allowActionsFor.includes(selectedGrade.subject_teacher_id)
                "
                @click="openDeleteModal"
            >
                Izbriši
            </DangerButton>
            <PrimaryButton
                v-if="
                    allowEdit &&
                    allowActionsFor.includes(selectedGrade.subject_teacher_id)
                "
                class="ml-3"
                @click="openEditModal"
            >
                Uredi
            </PrimaryButton>
            <SecondaryButton class="ml-3" @click="closeInfoModal">
                Zapri
            </SecondaryButton>
        </template>
    </DialogModal>

    <DialogModal :show="confirmingGradeDeletion" @close="closeDeleteModal">
        <template #title> Izbriši oceno? </template>

        <template #content>
            <InputError :message="formDelete.errors.delete" class="mt-2" />
        </template>

        <template #footer>
            <SecondaryButton @click="closeDeleteModal">
                Prekliči
            </SecondaryButton>

            <DangerButton class="ml-3" @click="deleteGrade">
                Izbriši oceno
            </DangerButton>
        </template>
    </DialogModal>

    <DialogModal :show="gradeEditing" @close="closeEditModal">
        <template #title> Uredi oceno </template>

        <template #content>
            <form @submit.prevent="">
                <div class="px-4 py-5 bg-white sm:p-6">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- Grade -->
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="grade" value="Ocena" />
                            <Select
                                id="grade"
                                v-model="formEdit.grade"
                                class="mt-1 block w-full"
                            >
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </Select>

                            <InputError
                                :message="formEdit.errors.grade"
                                class="mt-2"
                            />
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="note" value="Opombe" />
                            <TextInput
                                id="note"
                                v-model="formEdit.note"
                                type="text"
                                class="mt-1 block w-full"
                            />
                            <InputError
                                :message="formEdit.errors.note"
                                class="mt-2"
                            />
                            <InputError
                                :message="formEdit.errors.user"
                                class="mt-2"
                            />
                            <InputError
                                :message="formEdit.errors.subject"
                                class="mt-2"
                            />
                        </div>
                    </div>
                </div>
            </form>
        </template>

        <template #footer>
            <SecondaryButton @click="closeEditModal">
                Prekliči
            </SecondaryButton>

            <PrimaryButton class="ml-3" @click="editGrade">
                Shrani
            </PrimaryButton>
        </template>
    </DialogModal>
</template>
