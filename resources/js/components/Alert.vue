<template>
    <transition 
        enter-active-class="animate__animated animate__fadeInDown" 
        leave-active-class="animate__animated animate__fadeOutUp">
        <div class="fixed top-0 inset-x-0" @click="closeAlert()" v-if="modelValue" style="z-index:100">
            <div @focusout="closeAlert()"
                class="relative shadow-lg bg-white p-4 lg:w-1/3 md:w-3/4 md:rounded md:mx-auto my-10 border-gray-300 border-solid border">
                <div>
                    <div class="flex gap-4">
                        <i class="text-size-48px fas" :class="[statClass.icon, statClass.color]"></i>
                        <div class="flex-1">
                            <div class="text-xl font-medium capitalize" :class="statClass.color">
                                <span v-if="title != '' || title !== undefined">{{ title }}</span>
                                <span v-else>{{ status }}</span>
                            </div>
                            <div v-if="typeof message === 'object'">
                                <table>
                                    <tr v-for="(m, i) in message" :key="i">
                                        <td class="collapse uppercase text-sm text-gray-600">{{ i }}</td>
                                        <td class="pl-2 font-medium">
                                            <div class="max-h-40 break-all overflow-hidden">: {{ m }}</div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div v-else-if="typeof message === 'array'">
                                <div v-for="(m, i) in message" :key="i">{{ m }}</div>
                            </div>
                            <div v-else v-html="message"></div>
                        </div>
                    </div>
                </div>

                <div class="text-sm italic text-right text-gray-500 mt-2">
                    Click to close
                </div>
                <input type="text" ref="input" class="absolute w-0 -left-px">
            </div>
        </div>
    </transition>
</template>
<script>
export default {
    props: {
        modelValue: {type:Boolean, default:false},
        status: {type:String, default:'info'},
        title: {String,  default:''},
        message: {type:[String, Object, Array], default:''}
    },
    methods: {
        closeAlert () {
            this.$emit('update:modelValue', false)
        },
        focustInput () {
            this.$nextTick(() => {
                this.$refs.input.focus()
            })
        }
    },
    computed: {
        statClass () {
            var css = {}

            switch (this.status) {
                default:
                    css.color = 'text-blue-500'
                    css.icon = 'fa-info-circle'
                    break;
                case 'success':
                    css.color = 'text-green-500'
                    css.icon = 'fa-check-circle'
                    break;
                case 'warning':
                    css.color = 'text-yellow-500'
                    css.icon = 'fa-exclamation-circle'
                    break;
                case 'error':
                    css.color = 'text-red-500'
                    css.icon = 'fa-times-circle'
                    break;
            }

            return css
        },
    },
    watch: {
        modelValue (val) {
            if (val) this.focustInput()
        }
    }
}
</script>