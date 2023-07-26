<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import FormSection from "@/Components/FormSection.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DialogModal from "@/Components/DialogModal.vue";

const props = defineProps({
    hours: Object,
});

const editingHours = ref(false);
const parsedHours = ref(JSON.parse(props.hours.value));
const formHours = useForm({});

// edit hours

const closeHoursModal = () => {
    editingHours.value = false;
    formHours.reset();
};

const openHoursModal = () => {
    editingHours.value = true;
    parsedHours.value = JSON.parse(props.hours.value);
};

const editHours = () => {
    //remove empty rows
    parsedHours.value = parsedHours.value.filter(
        (item) => item.name !== "" && item.to !== "" && item.from !== ""
    );
    parsedHours.value.sort((a, b) => a.index - b.index);

    formHours
        .transform(() => ({
            value: JSON.stringify(parsedHours.value),
        }))
        .put(route("setting.update", "timetable.hours"), {
            preserveScroll: true,
            onSuccess: () => closeHoursModal(),
            onError: () => {},
        });
};
</script>

<template>
    <FormSection>
        <template #title> Ure urnikov </template>

        <template #description> nevem se ka tle napisat </template>

        <template #form>
            <table class="table-fixed mt-0 col-span-3">
                <thead
                    class="text-xs text-center text-gray-700 uppercase bg-gray-50"
                >
                    <tr>
                        <th scope="col" class="px-6 py-3">Indeks</th>
                        <th scope="col" class="px-6 py-3">Naziv ure</th>
                        <th scope="col" class="px-6 py-3">Od</th>
                        <th scope="col" class="px-6 py-3">Do</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <tr v-for="hour in JSON.parse(hours.value)">
                        <td class="px-2 border-r">
                            {{ hour.index }}
                        </td>
                        <td class="px-2 border-r">
                            {{ hour.name }}
                        </td>
                        <td class="px-2 border-r">
                            {{ hour.from }}
                        </td>
                        <td class="px-2">
                            {{ hour.to }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </template>

        <template #actions>
            <PrimaryButton @click="openHoursModal">
                Spremeni ure urnika
            </PrimaryButton>
        </template>
    </FormSection>

    <DialogModal :show="editingHours" @close="closeHoursModal">
        <template #title> Spremeni ure urnika </template>
        <template #content>
            <form @submit.prevent="">
                <div class="overflow-x-auto grid grid-cols-3 sm:rounded-md">
                    <table class="table-fixed mt-0 col-span-3">
                        <thead
                            class="text-xs text-left text-gray-700 uppercase bg-gray-50"
                        >
                            <tr>
                                <th scope="col" class="px-6 py-3">Indeks</th>
                                <th scope="col" class="px-6 py-3">Naziv ure</th>
                                <th scope="col" class="px-6 py-3">Od</th>
                                <th scope="col" class="px-6 py-3">Do</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(hour, index) in parsedHours">
                                <td>
                                    <TextInput
                                        :id="`${index}-index`"
                                        v-model="hour.index"
                                        type="text"
                                        class="mt-1 block w-full"
                                        @blur="
                                            parsedHours.sort(
                                                (a, b) => a.index - b.index
                                            )
                                        "
                                    />
                                </td>
                                <td>
                                    <TextInput
                                        :id="`${index}-name`"
                                        v-model="hour.name"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                </td>
                                <td>
                                    <TextInput
                                        :id="`${index}-from`"
                                        v-model="hour.from"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                </td>
                                <td>
                                    <TextInput
                                        :id="`${index}-to`"
                                        v-model="hour.to"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="col-span-3">
                        <PrimaryButton
                            class="px-2 py-1 m-2"
                            @click="
                                parsedHours.push({ name: '', to: '', from: '' })
                            "
                        >
                            Dodaj Vrstico
                        </PrimaryButton>
                    </div>

                    <div class="col-span-3">
                        <InputError
                            :message="formHours.errors.value"
                            class="mt-2"
                        />
                    </div>
                </div>
                <p class="py-2">
                    Prazne vrstice bodo ignorirane, tudi v primeru, ko niso na
                    koncu!
                </p>
                <p>Indeks:</p>
                <ul class="list-disc py-2 pl-4">
                    <li>
                        Vnosi urnika uporabljajo vrednost "indeks" za določitev,
                        v katero uro spadajo;
                    </li>
                    <li>
                        Priporočeno je, da se teh vrednosti ne spreminja, če že
                        imate določene urnike;
                    </li>
                    <li>
                        Če imate vnos urnika, z določenim indeksom, in ta indeks
                        s tega seznama odstranite, se vnos ne bo prikazal na
                        urniku;
                    </li>
                    <li>
                        Prav tako ni priporočeno, da imata dve (ali več) uri
                        isti indeks, ker bo to vnose podvojilo;
                    </li>
                    <li>Negativni indeksi so dovoljeni;</li>
                    <li>Lahko ima največ eno decimalno števko.</li>
                </ul>
            </form>
        </template>

        <template #footer>
            <SecondaryButton @click="closeHoursModal">
                Prekliči
            </SecondaryButton>

            <PrimaryButton class="ml-3" @click="editHours">
                Shrani
            </PrimaryButton>
        </template>
    </DialogModal>
</template>
