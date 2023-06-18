<script setup>
import Spinner from '@/Components/Spinner.vue';
import { ref, onMounted } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Select from '@/Components/Select.vue';

const props = defineProps({
    data: Array,
    subjects: Array,
    classId: Number,
    allowEdit: {
        type: Boolean,
        default: false,
    },
    buffer: Boolean
});

const emit = defineEmits(['refresh']);

const colNum = Array.from(Array(5).keys());
const rowNum = ref(Array.from(Array(6).keys()));

const itemData = ref({})
const addingTimetableEntry = ref(false);
const entryToInsert = ref(false);

const formAdd = useForm({
    subject: '',
    class: props.classId
});
const formRemove = useForm({});

const refreshTimetable = () => {
    emit('refresh')

    itemData.value = {}

    if (props.data.length) {
        //get max hour
        let maxHour = props.data.reduce((prev, current) => (prev.hour > current.hour) ? prev : current).hour + 1

        if (props.allowEdit)
            maxHour++

        rowNum.value = Array.from(Array(maxHour > 6 ? maxHour : 6).keys())
    }

    props.data.forEach((value) => {
        const tempUser = props.subjects.filter((sub) => value.subject_teacher.user_id == sub.user.id)[0].user
        const tempSubject = props.subjects.filter((sub) => value.subject_teacher.subject_id == sub.subject.id)[0].subject.name

        itemData.value[`${value.hour}-${value.day}`] = {
            subject: value.subject_teacher.custom_name ? value.subject_teacher.custom_name : tempSubject,
            fullname: `${tempUser.name} ${tempUser.surname}`,
            id: value.id
        }
    })
}

onMounted(() => {
    refreshTimetable()
})

// adding

const closeAddingModal = () => {
    addingTimetableEntry.value = false;
    entryToInsert.value = false;
    refreshTimetable()
    formAdd.reset()
};

const openAddingModal = (row, col) => {
    addingTimetableEntry.value = true;
    entryToInsert.value = [row, col];
};

const addTimetableEntry = () => {
    formAdd.transform(() => ({
        ...formAdd,
        hour: entryToInsert.value[0],
        day: entryToInsert.value[1],
    })).post(route('create.timetable'), {
        preserveScroll: true,
        onSuccess: () => closeAddingModal(),
        onError: () => {},
    });
};

// removing

const removeTimetableEntry = (id) => {
    formRemove.post(route('timetable.delete', id), {
        preserveScroll: true,
        onSuccess: () => refreshTimetable(),
        onError: () => {},
    });
};
</script>

<template>
    <div class="relative shadow">
        <Spinner :buffer="buffer" />

        <div class="overflow-x-auto grid grid-cols-3 sm:rounded-t-md">
            <table class="table-auto mt-0 col-span-3">
                <thead class="text-xs text-left text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 border-r"></th>
                        <th class="px-6 py-3 border-r">Pon</th>
                        <th class="px-6 py-3 border-r">Tor</th>
                        <th class="px-6 py-3 border-r">Sre</th>
                        <th class="px-6 py-3 border-r">Čet</th>
                        <th class="px-6 py-3 border-r">Pet</th>
                    </tr>
                </thead>
                <tbody class="text-xs md:text-sm lg:text-base">
                    <tr v-for="row in rowNum" class="bg-white border-b hover:bg-gray-100">
                        <td class="p-3 border-r text-center text-gray-600">{{ row }}. ura</td>
                        <td class="px-6 py-3 border-r" v-for="col in colNum">
                            <div v-if="!itemData[`${row}-${col}`] && allowEdit">
                                <SecondaryButton @click="openAddingModal(row, col)">
                                    Dodaj
                                </SecondaryButton>
                            </div>
                            <div v-if="itemData[`${row}-${col}`]">
                                <div>
                                    {{ itemData[`${row}-${col}`].subject }}
                                </div>
                                <div class="text-gray-600 text-xs">
                                    {{ itemData[`${row}-${col}`].fullname }}
                                </div>
                                <div class="flex justify-end px-1">
                                    <a class="cursor-pointer" v-if="allowEdit" @click="removeTimetableEntry(itemData[`${row}-${col}`].id)">
                                        <font-awesome-icon icon="fa-solid fa-trash-can" class="text-gray-700 px-2"/>
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <DialogModal :show="addingTimetableEntry" @close="closeAddingModal"> 
        <template #title>
            Dodaj vnos urnika
        </template>

        <template #content>
            <form @submit.prevent="">
                <div
                    class="px-4 py-5 bg-white sm:p-6"
                >
                    <div class="grid grid-cols-6 gap-6">
                        <!-- Subject -->
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="class" value="Predmet" />
                            <Select
                                id="subject"
                                v-model="formAdd.subject"
                                class="mt-1 block w-full"
                                autocomplete="subject"
                            >
                                <option v-for="subject in subjects" :value="subject.id">
                                    {{ `${subject.user.name} ${subject.user.surname} - ${subject.custom_name ? subject.custom_name : subject.subject.name}` }}
                                </option>
                            </Select>
                            <InputError :message="formAdd.errors.subject" class="mt-2" />
                        </div>
                    </div>
                </div>
            </form>
        </template>

        <template #footer>
            <SecondaryButton @click="closeAddingModal">
                Prekliči
            </SecondaryButton>

            <PrimaryButton
                class="ml-3"
                @click="addTimetableEntry"
            >
                Shrani
            </PrimaryButton>
        </template>
    </DialogModal>
</template>