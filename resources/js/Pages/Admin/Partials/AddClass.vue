<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import ActionMessage from '@/Components/ActionMessage.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Select from '@/Components/Select.vue';

const props = defineProps({
    users: Array,
});

const form = useForm({
    name: '',
    class_teacher: ''
});

const postNewClassData = () => {
    form.post(route('create.class'), {
        onSuccess: () => {
            form.reset()
        },
    });
};

</script>

<template>
    <FormSection @submitted="postNewClassData">
        <template #title>
            Dodaj razred
        </template>

        <template #description>
            nevem se ka tle napisat
        </template>

        <template #form>
            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="name" value="Naziv razreda" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="name"
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <!-- Class Teacher -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="class" value="Razrednik" />
                <Select
                    id="class_teacher"
                    v-model="form.class_teacher"
                    class="mt-1 block w-full"
                    autocomplete="class_teacher"
                >
                    <option v-for="user in users" :value="user.id">
                        {{ `${user.name} ${user.surname} - ${user.email}` }}
                    </option>
                </Select>
                <InputError :message="form.errors.class_teacher" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
            </PrimaryButton>
        </template>
    </FormSection>
</template>
