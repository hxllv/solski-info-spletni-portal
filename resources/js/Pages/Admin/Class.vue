<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Table from '@/Components/Table.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

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
            </div>
        </div>
    </AdminLayout>
</template>