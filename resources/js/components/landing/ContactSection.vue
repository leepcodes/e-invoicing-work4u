<script setup lang="ts">
import type { LucideIcon } from '@lucide/vue';
import { Mail, Phone, MapPin } from '@lucide/vue';
</script>

<template>

    <section id="contact" class="py-20 bg-gray-50">

        <div class="max-w-7xl mx-auto px-6">

            <div class="grid lg:grid-cols-2 gap-12">


                <div>
                    <h2 class="text-4xl font-extrabold text-gray-900">
                        Contact Us
                    </h2>
                    <p class="mt-4 text-gray-600">
                        Have questions? Our team is ready to help.
                    </p>
                    <div class="mt-8 space-y-4 text-gray-700">

                        <p>
                            <Mail class="inline-block mr-2" />
                            support@einvoicing.com
                        </p>
                        <p>
                            <Phone class="inline-block mr-2" />
                            +63 900 000 0000
                        </p>
                        <p>
                            <MapPin class="inline-block mr-2" />
                            Philippines
                        </p>

                    </div>

                </div>


                <form
                class="
                bg-white
                rounded-3xl
                shadow-xl
                p-8
                space-y-5
                text-gray-700
                "
                >
                    <h2 class="text-2xl font-bold text-gray-900">
                        Send us a message
                    </h2>

                    <label class="block text-gray-700 font-semibold">
                        Name
                    </label>
                    <input
                    class="w-full rounded-xl border-gray-900 border-2 p-2"
                    placeholder=" Enter Name"
                    />

                    <label class="block text-gray-700 font-semibold">
                        Email
                    </label>
                    <input
                    class="w-full rounded-xl border-gray-900 border-2 p-2"
                    placeholder=" Enter Email"
                    />

                    <label class="block text-gray-700 font-semibold">
                        Message
                    </label>
                    <textarea
                    class="w-full rounded-xl border-gray-900 border-2 p-2"
                    rows="5"
                    placeholder=" Message Here"
                    />


                    <button
                    class="
                    w-full
                    py-3
                    rounded-xl
                    bg-gradient-to-r
                    from-blue-600
                    to-indigo-600
                    text-white
                    font-semibold
                    "
                    >
                    Send Message
                    </button>


                </form>


            </div>

        </div>

    </section>

</template>
