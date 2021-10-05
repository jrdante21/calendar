<template>
    <div>
        <Alert v-model="alert.show" :status="alert.status" :title="alert.title" :message="alert.message"/>

        <div class="text-lg font-medium mb-5">Calendar</div>
        <div class="flex gap-10">
            <div class="flex-initial w-1/3">
                <div class="flex flex-col gap-4">
                    <div>
                        <label>Event</label>
                        <input type="text" class="input" v-model="formEvent">
                    </div>
                    <div>
                        <div class="flex gap-4">
                            <div class="flex-1">
                                <label>From</label>
                                <DatePicker v-model="dateFrom" :masks="{ input:'MMM DD, YYYY' }">
                                    <template v-slot="{ inputValue, inputEvents }">
                                        <input type="text" :value="inputValue" v-on="inputEvents" class="input">
                                    </template>
                                </DatePicker>
                            </div>
                            <div class="flex-1">
                                <label>To</label>
                                <DatePicker v-model="dateTo" :masks="{ input:'MMM DD, YYYY' }">
                                    <template v-slot="{ inputValue, inputEvents }">
                                        <input type="text" :value="inputValue" v-on="inputEvents" class="input">
                                    </template>
                                </DatePicker>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="flex flex-wrap gap-x-4 gap-y-2">
                            <div v-for="(d, i) in days" :key="i">
                                <input type="checkbox" name="dateDaysForm" :id="`dateDaysForm${i}`" :value="i" v-model="formDays">
                                <label :for="`dateDaysForm${i}`" class="link pl-2">{{ d }}</label>
                            </div>
                        </div>
                    </div>
                    <div>
                        <button :class="{'pointer-events-none cursor-not-allowed':disableBtn}" class="button button-blue" @click="submitEvents()">Save</button>
                    </div>
                </div>
            </div>
            <div class="flex-1">
                <div class="flex flex-col gap-5">
                    <div v-for="(events, month) in dataEvents" :key="month">
                        <div class="text-2xl font-bold mb-3">{{ moment(month, 'YYYYMMDD').format('MMM YYYY') }}</div>
                        <table class="table">
                            <tbody>
                                <template v-for="(e, i) in events" :key="i">
                                    <tr :class="{'bg-green-100': e.events !== null}">
                                        <td class="collapse">{{ moment(e.date).format('D ddd') }}</td>
                                        <td>
                                            <span v-if="e.events !== null">{{ e.events }}</span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { DatePicker } from 'v-calendar'
import Alert from './Alert.vue'
import moment from 'moment'
import axios from 'axios'
import validate from 'validate.js'
export default {
    components: { DatePicker, Alert },
    data () {
        return {
            moment,
            dateFrom: null,
            dateTo: null,
            days: {1:'Mon', 2:'Tue', 3:'Wed', 4:'Thu', 5:'Fri', 6:'Sat', 7:'Sun'},
            formDays: [],
            formEvent: '',
            dataEvents: [],

            alert: {
                show: false,
                status: 'info'
            },
            disableBtn: false
        }
    },
    created () {
        this.getEvents()
    },
    methods: {
        getEvents () {
            axios.get(`/api/events`)
            .then(res => {
                this.dataEvents = res.data
            })
            .catch(err => {
                console.log(err)
            })
        },
        submitEvents () {
            const valid = validate({
                date_from: this.dateFrom,
                date_to: this.dateTo,
                days: this.formDays,
                event: this.formEvent,
            }, {
                date_from: {
                    presence: {
                        allowEmpty: false,
                        message: "^Date From is required."
                    }
                },
                date_to: {
                    presence: {
                        allowEmpty: false,
                        message: "^Date To is required."
                    }
                },
                days: {
                    presence: {
                        allowEmpty: false,
                        message: "is required."
                    }
                },
                event: {
                    presence: {
                        allowEmpty: false,
                        message: "is required."
                    }
                }
            }, {format:'flat'});

            if (valid !== undefined) {
                this.alert = {
                    show: true,
                    status: 'warning',
                    title: 'Field error',
                    message: valid.join('<br>')
                }
                return false
            }

            const data = {
                date_from: this.dateFrom.toJSON(),
                date_to: this.dateTo.toJSON(),
                days: Object.values(this.formDays),
                event: this.formEvent
            }

            this.disableBtn = true
            axios.post(`/api/save_events`, data)
            .then(() => {
                this.alert = {
                    show: true,
                    status: 'success',
                    title: 'Event successfully saved.',
                }

                this.disableBtn = false
                this.getEvents()
            })
            .catch(err => {
                this.disableBtn = false
                console.log(err)
            })
        }
    },
}
</script>