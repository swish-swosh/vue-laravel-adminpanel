<template>
    <b-container fluid>
        <validation-observer ref="observer" v-slot="{ invalid }">
            <b-form @submit="onSubmit($event, formData)">
                <b-row class="mb-3">
                    <b-col cols="12" md="6">
                    <ValidationProvider name="name" rules="required" v-slot="validationContext">
                    <b-form-group class="mt-2" id="name" label="Name" label-for="i-name" rules="required">
                        <b-form-input
                        id="i-name"
                        v-model="formData.data.name"
                        :state="getValidationState(validationContext)"
                        aria-describedby="name-input-feedback"
                        placeholder="Monitor name"
                        ></b-form-input>
                        <b-form-invalid-feedback id="name-input-feedback">
                        {{ validationContext.errors[0] }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                    </ValidationProvider>
                    </b-col>

                    <b-col cols="12" md="6">
                    <ValidationProvider name="address" rules="required" v-slot="validationContext">
                    <b-form-group class="mt-2" id="address" label="Address" label-for="i-address" rules="required">
                        <b-form-input
                        id="i-address"
                        v-model="formData.data.address"
                        :state="getValidationState(validationContext)"
                        aria-describedby="address-input-feedback"
                        placeholder="where to fetch data"
                        ></b-form-input>
                        <b-form-invalid-feedback id="address-input-feedback">
                        {{ validationContext.errors[0] }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                    </ValidationProvider>
                    </b-col>
                </b-row>

                <b-row class="mb-3">
                    <b-col cols="12" md="6">
                    <b-form-group class="mt-2" id="login" label="Login" label-for="i-login">
                        <b-form-input
                        v-model="formData.data.login"
                        id="i-login"
                        aria-describedby="login-input-feedback"
                        placeholder="Service login name"
                        ></b-form-input>
                    </b-form-group>
                    </b-col>

                    <b-col cols="12" md="6">
                    <b-form-group class="mt-2" id="password" label="Password" label-for="i-password">
                        <b-form-input
                        id="i-password"
                        type="password"
                        aria-describedby="password-input-feedback"
                        placeholder="Service password"
                        ></b-form-input>
                    </b-form-group>
                    </b-col>
                </b-row>

                <b-row class="mb-3">
                    <b-col>
                        <label class="mb-1" label-for="i-chart-plot-input">Chart plot items:</label>
                        <b-input-group id="input-group-5">
                        <b-form-textarea
                            rows="3"
                            max-rows="20"
                            id="i-chart-plot-input"
                            name="chart-plot-input"
                            placeholder="Items to be plotted on chart, each row"
                            v-model="formData.data.chartPlots"
                            aria-describedby="chart-plot-input-feedback"
                        ></b-form-textarea>
                        </b-input-group>
                    </b-col>
                </b-row>

                <b-row class="mb-1 message-box">
                    <b-col>{{message1}}<br><br>
                    <ul class="monospaced">
                        <li v-if="message2">{{message2}}</li>
                        <li v-if="message3">{{message3}}</li>
                        <li v-if="message2||message3">{{message4}}</li>
                    </ul>
                    </b-col>
                </b-row>

                <b-row class="mb-3">
                    <b-col cols="4">
                    <label for="month" class="grayout" v-bind:class="{ active: formData.data.pollingInterval.month == -1 }">
                        {{dMonthIntro+' '+dMonth}}
                    </label>
                    <b-form-input id="month" @update="updateNextRun(formData.data.pollingInterval)" v-model="formData.data.pollingInterval.month" type="range" min="-1" max="11" step="1"></b-form-input>
                   </b-col>
                    <b-col cols="8">
                    <label for="day" class="grayout" v-bind:class="{ active: formData.data.pollingInterval.day == -1 }">
                        {{dDayIntro+' '+dDay}}
                    </label>
                        <b-form-input id="day" @update="updateNextRun(formData.data.pollingInterval)" v-model="formData.data.pollingInterval.day" type="range" min="-1" :max="30" step="1"></b-form-input>
                    </b-col>
                </b-row>

                <b-row class="mb-3">
                    <b-col cols="4">
                        <label for="hour" class="grayout" v-bind:class="{ active: formData.data.pollingInterval.hour == -1 }">
                            {{dHourIntro }}<span v-if="formData.data.pollingInterval.hour != -1">{{formData.data.pollingInterval.hour.toString().padStart(2, '0')+' '+dHour}}</span> 
                        </label>
                        <b-form-input id="hour" @update="updateNextRun(formData.data.pollingInterval)" v-model="formData.data.pollingInterval.hour" type="range" min="-1" max=23 step="1"></b-form-input>
                    </b-col>
                    <b-col cols="4">
                        <label for="minute" class="grayout" v-bind:class="{ active: formData.data.pollingInterval.minute == -1 }">
                            {{dMinuteIntro }}<span v-if="formData.data.pollingInterval.minute != -1"> {{ dMinute}}</span>
                        </label>
                        <b-form-input id="minute" @update="updateNextRun(formData.data.pollingInterval)" v-model="formData.data.pollingInterval.minute" type="range" min="-1" max="59" step="1"></b-form-input>
                    </b-col>
                    <b-col cols="4">
                        <label for="second" class="grayout active">
                            {{dSecondIntro }}<span v-if="formData.data.pollingInterval.second != -1">{{formData.data.pollingInterval.second.toString().padStart(2, '0')+' '+dSecond}}</span>
                        </label>
                        <b-form-input id="second" @update="updateNextRun(formData.data.pollingInterval)" v-model="formData.data.pollingInterval.second" type="range" min="-1" max="59" step="1"></b-form-input>
                    </b-col>
                </b-row>

                <b-row class="">
                    <b-col>
                    <ValidationProvider name="description" rules="" v-slot="validationContext">
                        <label class="mb-1" label-for="description-input">Description:</label>
                        <b-input-group id="input-group-4">
                        <b-form-textarea
                            rows="3"
                            max-rows="5"
                            id="description-input"
                            name="description-input"
                            placeholder=""
                            v-model="formData.data.description"
                            :state="getValidationState(validationContext)"
                            aria-describedby="description-input-feedback"
                        ></b-form-textarea>
                        <b-form-invalid-feedback id="description-input-feedback">
                            {{ validationContext.errors[0] }}
                        </b-form-invalid-feedback>
                        </b-input-group>
                    </ValidationProvider>
                    </b-col>
                </b-row>

                <b-row class="text-right mt-4 w-100">
                    <b-col>
                        <p>{{feedback}}</p>
                    </b-col>
                </b-row>
                <b-row class="mt-3 mb-0 w-100">
                    <b-col class="text-left">
                        <b-button @click="$emit('actions', 'closeModal')" variant="primary" size="md" class="float-left">
                        <span>Cancel</span>
                    </b-button>
                    <b-button type="submit" :disabled="invalid" variant="primary" size="md" class="float-right">
                        <span v-if="formData.id">Update monitor</span>
                        <span v-else>Add monitor</span>
                    </b-button>
                    </b-col>
                </b-row>
            </b-form>
        </validation-observer>
    </b-container>
</template>

<script>
    import { ValidationProvider } from 'vee-validate'
    import { ValidationObserver } from 'vee-validate'
    import { mapGetters, mapActions } from 'vuex'
    export default {
        name: 'monitor-form',
        props: {
            resource: Object,
            feedback: String
        },
        components: {
            ValidationProvider,
            ValidationObserver
        },
        data() {
            return {
                formData: {
                    id: false,
                    data: {
                        name: '',
                        description: '',
                        address: '',
                        chartPlots: '',
                        login: '',
                        password: '',
                        nextRun: '',
                        pollingInterval: {
                            month: -1,
                            day: -1,
                            hour: -1,
                            minute: -1,
                            second: -1
                        }
                    }
                },
                nextRun: '',
                message1: '',
                message2: '',
                message3: '',
                message4: '',
                dMonth: '',
                dMonthIntro: '',
                dDay: '',
                dDayIntro: '',
                dHour: '',
                dHourIntro: '',
                dMinute: '',
                dMinuteIntro: '',
                dSecond: '',
                dSecondIntro: ''            
            }
        },
        mounted() {
            // called when parent data is available - no worries!
            this.formData = this.resource
            this.updateNextRun(this.formData.data.pollingInterval)
        },
        methods: {
            updateNextRun(pi) {
                // create select token for logic operations
                // parse form input text values to integer
                let token = ''
                for (const [key, value] of Object.entries(pi)) {
                    token += value == -1 ? '0' : '1'
                    pi[key] = parseInt(pi[key])
                }

                // calculate current date and times
                const cYear = new Date().getFullYear(),
                        cMonth = new Date().getMonth(),
                        cDay = new Date().getDate(),
                        cHour = new Date().getHours(),
                        cMinute = new Date().getMinutes(),
                        cSecond = new Date().getSeconds()

                // calculate date, time singular, plural tails
                this.dMonth   = pi.month == -1 ? ''   : pi.month == 0 ?   'month' : (pi.month+1) + 'months'
                this.dDay     = pi.day == -1 ? ''     : pi.day == 0 ?     'day' : (pi.day+1) + ' days'
                this.dHour    = pi.hour == -1 ? ''    : pi.hour == 0 ?    'hour' : (pi.hour+1) +  'hours'
                this.dMinute  = pi.minute == -1 ? ''  : pi.minute == 0 ?  'minute' : (pi.minute+1) +  'minutes'
                this.dSecond  = pi.second == -1 ? ''  : pi.second == 0 ?  'second' : (pi.second+1) +  'seconds'

                this.dMonthIntro = 'Months:'
                this.dDayIntro = 'Days:'
                this.dHourIntro = 'Hours:'
                this.dMinuteIntro = 'Minutes:'
                this.dSecondIntro = 'Seconds:'        

                const dNoRun = 'Set the interval to a specific date / time',
                        dDateMessage = 'Monitoring wil start on:',
                        dTimeMessage = 'Monitoring wil run each:'

                let dDate = true, dTime = true, dMessage = '',
                    dMessageTail = '...'

                let now = new Date()
                let nextRun = new Date()
                let nextRunNext = new Date()

                // get date part
                let t = token.slice(0,-3)
                switch(t){
                    case '00':
                    dDate = false
                    break
                    case '01':
                    nextRun.setDate(cDay+pi.day+1)
                    nextRunNext.setDate(cDay+((pi.day+1)*2))
                    this.dDayIntro = 'Every:'
                    break
                    case '10':
                    nextRun.setMonth(cMonth+pi.month+1)
                    nextRunNext.setMonth(cMonth+((pi.month+1)*2))
                    this.dMonthIntro = 'Every:'
                    break
                    case '11':
                    nextRun.setMonth(cMonth+pi.month+1)
                    nextRun.setDate(cDay+pi.day+1)
                    nextRunNext.setMonth(cMonth+((pi.month+1)*2))
                    nextRunNext.setDate(cDay+((pi.day+1)*2))
                    this.dMonthIntro = 'Every:'
                    this.dDayIntro = 'plus'
                    break
                }

                // get time part
                t = token.slice(2)
                switch(t){
                    case '000':
                    dTime = false          
                    break
                    case '001':
                    nextRun.setSeconds(cSecond+pi.second+1)
                    nextRunNext.setSeconds(cSecond+((pi.second+1)*2))
                    this.dSecondIntro = 'Every:'
                    break
                    case '010':
                    nextRun.setMinutes(cMinute+pi.minute+1)
                    nextRunNext.setMinutes(cMinute+((pi.minute+1)*2))
                    this.dMinuteIntro = 'Every:'
                    break
                    case '011':
                    nextRun.setMinutes(cMinute+pi.minute+1)
                    nextRunNext.setMinutes(cMinute+((pi.minute+1)*2))
                    nextRun.setSeconds(cSecond+pi.second+1)
                    nextRunNext.setSeconds(cSecond+((pi.second+1)*2))
                    this.dMinuteIntro = 'Every:'
                    this.dSecondIntro = 'Every:'
                    break
                    case '100':
                    nextRun.setHours(cHour+pi.hour+1)
                    nextRunNext.setHours(cHour+((pi.hour+1)*2))
                    this.dHourIntro = 'Every:'
                    break
                    break
                    case '101':
                    nextRun.setHours(cHour+pi.hour+1)
                    nextRunNext.setHours(cHour+((pi.hour+1)*2))
                    nextRun.setSeconds(cSecond+pi.second+1)
                    nextRunNext.setSeconds(cSecond+((pi.second+1)*2))
                    this.dHourIntro = 'Every:'
                    this.dSecondIntro = 'Every:'
                    break
                    case '110':
                    nextRun.setHours(cHour+pi.hour+1)
                    nextRunNext.setHours(cHour+((pi.hour+1)*2))
                    nextRun.setMinutes(cMinute+pi.minute+1)
                    nextRunNext.setMinutes(cMinute+((pi.minute+1)*2))
                    this.dHourIntro = 'Every:'
                    this.dMinuteIntro = 'Every:'
                    break
                    case '111':
                    nextRun.setHours(cHour+pi.hour+1)
                    nextRunNext.setHours(cHour+((pi.hour+1)*2))
                    nextRun.setMinutes(cMinute+pi.minute+1)
                    nextRunNext.setMinutes(cMinute+((pi.minute+1)*2))
                    nextRun.setSeconds(cSecond+pi.second+1)
                    nextRunNext.setSeconds(cSecond+((pi.second+1)*2))
                    this.dHourIntro = 'Every:'
                    this.dMinuteIntro = 'Every:'
                    this.dSecondIntro = 'Every:'
                    break
                }

                if(!dDate && !dTime) {
                    dMessage = dNoRun
                    nextRun = ''
                    nextRunNext = ''
                }
                else if(dTime) dMessage = dTimeMessage
                    else dMessage = dDateMessage

                this.message1 = dMessage
                this.message2 = nextRun
                this.message3 = nextRunNext
                this.message4 = dMessageTail
                
                // format date for db
                let formattedDate = nextRun.getFullYear() + "-" + (nextRun.getMonth() + 1) + "-" + nextRun.getDate() 
                + " " + nextRun.getHours() + ":" + nextRun.getMinutes() + ":00" // + nextRun.getSeconds() 

                this.formData.nextRun = formattedDate
            },
            getValidationState({ dirty, validated, valid = null }) {
                return dirty || validated ? valid : null
            },
            onSubmit(evt, formData) {
                evt.preventDefault()

                // update or new? (id = new || id > 0 )
                let self = this
                if(this.id === 'new') {
                this.createMonitor(formData).then(function(response){
                    if(response.status === 201) {
                    self.$emit('actions', 'formUpdated-closeModal')
                    } else
                    {
                    self.feedback = response.message + ', please check and try again'
                    }
                })
                } else
                {

                // patch monitor, after edit
                this.patchMonitor(formData).then(function(response){
                    if(response.status === 200) {
                    self.$emit('actions', 'formUpdated-closeModal')
                    } else
                    {
                    self.feedback = response.message + ', please check and try again'
                    }
                })
                }
            }
        }
    }
</script>

<style scoped>
    /* display date - time monospaced for readability */
    ul.monospaced {
        font-family: monospace;
    }

    .message-box {
        min-height: 9rem;
    }
</style>