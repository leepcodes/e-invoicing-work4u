<script setup lang="ts">
import { useAOS } from "@/composables/useAOS";

useAOS();

const plans = [
    {
        name: "Starter",
        price: "₱499",
        features: ["100 invoices/month", "Customer management", "Email support"],
    },
    {
        name: "Professional",
        price: "₱999",
        features: ["Unlimited invoices", "Reports & analytics", "Priority support"],
        popular: true,
    },
    {
        name: "Enterprise",
        price: "₱1999",
        features: ["Advanced security", "Multiple users", "Dedicated support"],
    },
];
</script>

<template>
    <section id="pricing" class="bg-white py-20">
        <div class="mx-auto max-w-7xl px-6">
            <div class="text-center" data-aos="fade-up">
                <h2 class="text-4xl font-extrabold text-gray-900">Simple Pricing</h2>
                <p class="mt-3 text-gray-600">Choose the plan that fits your business.</p>
            </div>

            <div class="mt-12 grid gap-8 lg:grid-cols-3">
                <div
                    v-for="(plan, index) in plans"
                    :key="plan.name"
                    class="relative rounded-3xl border p-8 shadow-sm transition duration-300 hover:-translate-y-2 hover:border-blue-600 hover:shadow-xl"
                    :class="plan.popular ? 'border-blue-600 shadow-xl' : 'border-gray-200'"
                    data-aos="fade-up"
                    :data-aos-delay="index * 150"
                    data-aos-duration="900"
                >
                    <span
                        v-if="plan.popular"
                        class="absolute right-5 top-5 rounded-full bg-blue-600 px-3 py-1 text-xs text-white"
                        data-aos="zoom-in"
                    >
                        Popular
                    </span>

                    <h3 class="text-2xl font-bold text-black">{{ plan.name }}</h3>
                    <p class="mt-4 text-4xl font-extrabold text-blue-600">{{ plan.price }}</p>

                    <ul class="mt-6 space-y-3">
                        <li
                            v-for="(item, featureIndex) in plan.features"
                            :key="item"
                            class="text-gray-600"
                            data-aos="fade-right"
                            :data-aos-delay="featureIndex * 100"
                        >
                            ✓ {{ item }}
                        </li>
                    </ul>

                    <button
                        class="mt-8 w-full rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 py-3 font-semibold text-white"
                        data-aos="zoom-in"
                        data-aos-delay="400"
                    >
                        Choose Plan
                    </button>
                </div>
            </div>
        </div>
    </section>
</template>

