<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Table from '@/Components/Table.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DialogModal from '@/Components/DialogModal.vue';

const props = defineProps({
    sClass: Object,
    classTeacher: Object,
    users: Object,
    params: Object,
    middleware: Array,
});

const formFilter = useForm({
    term: props.params.term,
});

const hatedProps = ['created_at', 'current_team_id', 'email_verified_at', 'role_id', 'name', 'surname', 'two_factor_confirmed_at', 'updated_at'];

const usersMod = computed(() => {
    let newUsers = JSON.parse(JSON.stringify(props.users));

    newUsers.data.forEach(user => {
        user.fullname = `${user.name} ${user.surname}`

        hatedProps.forEach(prop => {
            delete user[prop]
        })
    })

    return newUsers
});
</script>

<template>
    <AdminLayout title="Dashboard" :backButtonURL="route('classes')" :header="`Učenci razreda ${sClass.class_name}`" v-slot="layout">
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div>
                <h2 class="pb-6 px-2 text-xl text-gray-800 leading-tight">
                    Razrednik: {{ `${classTeacher.name} ${classTeacher.surname} - ${classTeacher.email}` }}
                </h2>

                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="mt-5 md:mt-0 md:col-span-1 p-2">
                        <InputLabel for="term" value="Iskalni niz" />
                        <TextInput
                            id="term"
                            v-model="formFilter.term"
                            type="text"
                            class="mt-1 block w-full"
                            autocomplete="term"
                        />
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2 p-2 text-right flex items-end justify-end">
                        <Link preserve-scroll preserve-state :href="route('users')" :data="{ page: 1, term: '' }" class="mr-1" @click="formFilter.reset()">
                            <SecondaryButton>
                                    Reset
                            </SecondaryButton>
                        </Link>

                        <Link preserve-scroll preserve-state :href="route('users')" :data="{ page: 1, term: formFilter.term }">
                            <PrimaryButton>
                                    Apply
                            </PrimaryButton>
                        </Link>
                    </div>
                </div>

                <Table :data="usersMod" :headerNames="['Ime', 'Email']" 
                    :sortedAs="['fullname', 'email']" 
                    :allowEdit="false" :allowDelete="false" :allowMultiActions="true"
                    :query="{ term: formFilter.term }"
                    :buffer="layout.buffer"
                    @selectedChange="console.log"
                />

                <!-- todo: adding students to class -->
                <!-- <DialogModal :show="typeof showMultiModal == 'string'" @close="closeMultiModal"> 
                    <template #title v-if="showMultiModal === 'role'">
                        Dodeli skupino pravic
                    </template>
                    <template #content v-if="showMultiModal === 'class'">
                        <form @submit.prevent="">
                            <div class="px-4 py-5 bg-white sm:p-6">
                                <div class="grid grid-cols-6 gap-6">
                                    <div class="col-span-6 sm:col-span-4">
                                        <InputLabel for="class" value="Razred" />
                                        <Select
                                            id="class"
                                            v-model="formMulti.class"
                                            class="mt-1 block w-full"
                                            autocomplete="role"
                                        >
                                            <option value="-1">/</option>
                                            <option v-for="sClass in classes" :value="sClass.id">{{sClass.class_name}}</option>
                                        </Select>
                                        <InputError :message="formMulti.errors.class" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                        </form>
                    </template>

                    <template #footer v-if="showMultiModal === 'role' || showMultiModal === 'class'">
                        <SecondaryButton @click="closeMultiModal">
                            Cancel
                        </SecondaryButton>

                        <PrimaryButton
                            class="ml-3"
                            @click="multiActionDo(showMultiModal)"
                        >
                            Save
                        </PrimaryButton>
                    </template>
                </DialogModal> -->
            </div>
        </div>
    </AdminLayout>
</template>