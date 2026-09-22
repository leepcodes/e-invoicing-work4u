```vue
<script setup lang="ts">
import { useAOS } from "@/composables/useAOS";
import { FileText, Users, CreditCard, BarChart3, Bot, Cloud } from '@lucide/vue';
import type { LucideIcon } from '@lucide/vue';

useAOS();

interface Feature {
    title: string;
    desc: string;
    icon: LucideIcon;
}

const features: Feature[] = [
    { title: "Invoice Management", desc: "Create professional invoices and track payments easily.", icon: FileText },
    { title: "Customer Management", desc: "Store customer information and billing history.", icon: Users },
    { title: "Payment Tracking", desc: "Monitor paid, pending, and overdue invoices.", icon: CreditCard },
    { title: "Reports & Analytics", desc: "Get insights using real-time financial reports.", icon: BarChart3 },
    { title: "Automation", desc: "Reduce manual work with automated workflows.", icon: Bot },
    { title: "Cloud Access", desc: "Access your billing anywhere anytime.", icon: Cloud },
];
</script>

<template>
    <section id="feature" class="bg-gray-50 py-20">
        <div class="mx-auto max-w-7xl px-6">
            <div class="mx-auto max-w-2xl text-center" data-aos="fade-up">
                <h2 class="text-4xl font-extrabold text-gray-900">Powerful Features</h2>
                <p class="mt-4 text-gray-600">Everything you need to manage your invoices efficiently.</p>
            </div>

            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="(feature, index) in features" :key="feature.title" class="rounded-3xl border border-gray-100 bg-white p-7 shadow-sm transition hover:shadow-xl" data-aos="fade-up" :data-aos-delay="index * 100" data-aos-duration="800">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50">
                        <component :is="feature.icon" class="h-7 w-7 text-blue-600" />
                    </div>

                    <h3 class="mt-5 text-xl font-bold text-gray-900">{{ feature.title }}</h3>
                    <p class="mt-3 text-gray-500">{{ feature.desc }}</p>
                </div>
            </div>
        </div>
    </section>
</template>
```
