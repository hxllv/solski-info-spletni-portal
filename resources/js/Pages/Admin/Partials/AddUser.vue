<script setup>
import { useForm } from '@inertiajs/vue3';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Select from '@/Components/Select.vue';
import ActionMessage from '@/Components/ActionMessage.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    roles: Array,
    classes: Array,
    params: Object,
    buffer: Boolean
});

const form = useForm({
    name: '',
    surname: '',
    email: '',
    role: '',
    class: ''
});

const postNewUserData = () => {
    form.post(route('invite.user'), {
        onSuccess: () => {
            form.reset()
        },
    });
};
</script>

<template>
    <FormSection @submitted="postNewUserData">
        <template #title>
            Dodaj uporabnika
        </template>

        <template #description>
            <p>Uporabniku bo na dani email naslov poslan povabilo za registracijo.</p>
            <p class="pt-2">Otroke lahko dodate uporabniku po tem ko mu pošljete povabilo.</p>
        </template>

        <template #form>
            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="name" value="Ime" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="name"
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <!-- Surname -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="surname" value="Priimek" />
                <TextInput
                    id="surname"
                    v-model="form.surname"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="surname"
                />
                <InputError :message="form.errors.surname" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="email"
                />
                <InputError :message="form.errors.email" class="mt-2" />
            </div>

            <!-- Role -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="role" value="Skupina pravic" />
                <Select
                    id="role"
                    v-model="form.role"
                    class="mt-1 block w-full"
                    autocomplete="role"
                >
                    <option v-for="role in roles" :value="role.id">{{role.name}}</option>
                </Select>
                <InputError :message="form.errors.role" class="mt-2" />
            </div>

            <!-- class -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="class" value="Razred" />
                <Select
                    id="class"
                    v-model="form.class"
                    class="mt-1 block w-full"
                    autocomplete="class"
                >
                    <option value="-1">/</option>
                    <option v-for="sClass in classes" :value="sClass.id">{{sClass.name}}</option>
                </Select>
                <InputError :message="form.errors.class" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Povabilo poslano!
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Pošlji povabilo
            </PrimaryButton>
        </template>
    </FormSection>
</template>
