<script setup lang="ts">
import {
    FileText,
    Users,
    CreditCard,
    BarChart3,
    Bot,
    Cloud,
} from '@lucide/vue';

import type { LucideIcon } from '@lucide/vue';

interface Feature {
    title: string;
    desc: string;
    icon: LucideIcon;
}

const features: Feature[] = [
    {
        title: "Invoice Management",
        desc: "Create professional invoices and track payments easily.",
        icon: FileText,
    },
    {
        title: "Customer Management",
        desc: "Store customer information and billing history.",
        icon: Users,
    },
    {
        title: "Payment Tracking",
        desc: "Monitor paid, pending, and overdue invoices.",
        icon: CreditCard,
    },
    {
        title: "Reports & Analytics",
        desc: "Get insights using real-time financial reports.",
        icon: BarChart3,
    },
    {
        title: "Automation",
        desc: "Reduce manual work with automated workflows.",
        icon: Bot,
    },
    {
        title: "Cloud Access",
        desc: "Access your billing anywhere anytime.",
        icon: Cloud,
    },
];
</script>

<template>

    <section id="feature" class="py-20 bg-gray-50">

        <div class="max-w-7xl mx-auto px-6">

            <div class="text-center max-w-2xl mx-auto">
                <h2 class="text-4xl font-extrabold text-gray-900">
                    Powerful Features
                </h2>
                <p class="mt-4 text-gray-600">
                    Everything you need to manage your invoices efficiently.
                </p>
            </div>


            <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">

                <div
                v-for="feature in features"
                :key="feature.title"
                class="
                bg-white
                p-7
                rounded-3xl
                shadow-sm
                border
                border-gray-100
                hover:shadow-xl
                transition
                "
                >


                    <div class="
                    w-14
                    h-14
                    rounded-2xl
                    bg-blue-50
                    flex
                    items-center
                    justify-center
                    text-2xl
                    ">
                        <component
                            :is="feature.icon"
                            class="w-7 h-7 text-blue-600"
                        />
                    </div>


                    <h3 class="mt-5 text-xl font-bold text-gray-900">
                    {{feature.title}}
                    </h3>


                    <p class="mt-3 text-gray-500">
                    {{feature.desc}}
                    </p>


                </div>


            </div>


        </div>

    </section>

</template>
