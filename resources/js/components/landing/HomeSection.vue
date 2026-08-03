<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <section id="home" class="relative overflow-hidden bg-white">

        <!-- Background Blur -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-100 rounded-full blur-3xl opacity-40"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-indigo-100 rounded-full blur-3xl opacity-40"></div>

        <div class="max-w-7xl mx-auto px-6 py-20 lg:py-28">

            <div class="
                grid
                lg:grid-cols-2
                gap-14
                items-center
            ">

                <!-- LEFT CONTENT -->
                <div class="text-center lg:text-left">


                    <div
                        class="
                        inline-flex
                        items-center
                        gap-2
                        px-4
                        py-2
                        rounded-full
                        bg-blue-50
                        text-blue-600
                        text-sm
                        font-semibold
                        mb-6
                        "
                    >

                        <span class="w-2 h-2 bg-blue-600 rounded-full"></span>

                        Smart Digital Billing Platform

                    </div>



                    <h1
                        class="
                        text-4xl
                        sm:text-5xl
                        lg:text-6xl
                        font-extrabold
                        tracking-tight
                        text-gray-900
                        leading-tight
                        "
                    >

                        Simplify Your

                        <span
                            class="
                            bg-gradient-to-r
                            from-blue-600
                            to-indigo-600
                            bg-clip-text
                            text-transparent
                            "
                        >
                            Invoice Management
                        </span>

                    </h1>


                    <p
                        class="
                        mt-6
                        text-lg
                        text-gray-600
                        max-w-xl
                        mx-auto
                        lg:mx-0
                        leading-relaxed
                        "
                    >
                        Create invoices, manage customers, track payments,
                        and automate your billing workflow with a powerful
                        e-invoicing solution built for modern businesses.

                    </p>



                    <!-- BUTTONS -->
                    <div
                        class="
                        mt-8
                        flex
                        flex-col
                        sm:flex-row
                        gap-4
                        justify-center
                        lg:justify-start
                        "
                    >


                        <Link
                            href="/register"
                            class="
                            px-8
                            py-3.5
                            rounded-xl
                            text-white
                            font-semibold
                            bg-gradient-to-r
                            from-blue-600
                            to-indigo-600
                            shadow-lg
                            shadow-blue-500/30
                            hover:shadow-xl
                            hover:-translate-y-1
                            transition-all
                            duration-300
                            "
                        >
                            Start Free Trial
                        </Link>



                        <Link
                            href="/about"
                            class="
                            px-8
                            py-3.5
                            rounded-xl
                            border
                            border-gray-200
                            text-gray-700
                            font-semibold
                            hover:bg-gray-50
                            transition
                            "
                        >
                            Learn More
                        </Link>


                    </div>




                    <!-- Stats -->
                    <div
                        class="
                        mt-10
                        grid
                        grid-cols-3
                        gap-5
                        max-w-lg
                        mx-auto
                        lg:mx-0
                        "
                    >

                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">
                                99%
                            </h3>
                            <p class="text-sm text-gray-500">
                                Accuracy
                            </p>
                        </div>


                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">
                                10K+
                            </h3>
                            <p class="text-sm text-gray-500">
                                Invoices
                            </p>
                        </div>


                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">
                                24/7
                            </h3>
                            <p class="text-sm text-gray-500">
                                Support
                            </p>
                        </div>


                    </div>


                </div>

                <!-- RIGHT IMAGE -->
                <div class="relative flex justify-center lg:justify-end">

                    <!-- Glow Background -->
                    <div
                        class="
                        absolute
                        inset-0
                        bg-blue-100
                        rounded-full
                        blur-3xl
                        opacity-40
                        "
                    ></div>


                    <!-- Hero Image -->
                    <img
                        src="#"
                        alt="E-Invoicing Dashboard"
                        class="
                        relative
                        w-full
                        max-w-lg
                        lg:max-w-xl
                        rounded-3xl
                        shadow-2xl
                        hover:-translate-y-2
                        transition-all
                        duration-500
                        "
                    />


                    <!-- Floating Card -->
                    <div
                        class="
                        absolute
                        -bottom-5
                        -left-5
                        bg-white
                        rounded-2xl
                        shadow-xl
                        border
                        border-gray-100
                        px-5
                        py-4
                        hidden
                        sm:block
                        "
                    >

                        <p class="text-sm text-gray-500">
                            Payments Processed
                        </p>

                        <h3 class="text-2xl font-bold text-blue-600">
                            ₱500K+
                        </h3>

                    </div>


                </div>

            </div>


        </div>


    </section>
</template>
