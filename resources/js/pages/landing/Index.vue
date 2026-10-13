<script setup lang="ts">
import HomeSection from '@/components/landing/HomeSection.vue';
import FeatureSection from '@/components/landing/FeatureSection.vue';
import PricingSection from '@/components/landing/PricingSection.vue';
import ContactSection from '@/components/landing/ContactSection.vue';
import AboutSection from '@/components/landing/AboutSection.vue';
import ProductSection from '@/components/landing/ProductSection.vue';
import TestimonialSection from '@/components/landing/TestimonialSection.vue';
import CTASection from '@/components/landing/CTASection.vue';

interface Country {
    id: number;
    code: string;
    name: string;
    phone_code: string | null;
}

const props = defineProps<{
    countries: Country[];
}>();
</script>

<template>

    <HomeSection :countries="props.countries" />

    <AboutSection />

    <FeatureSection />

    <ProductSection />

    <TestimonialSection />

    <PricingSection />

    <ContactSection />

    <CTASection :countries="props.countries" />
</template>
