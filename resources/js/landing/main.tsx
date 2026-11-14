import React from 'react';
import { createRoot } from 'react-dom/client';
import Home from '@/app/page';
import About from '@/app/about/page';
import Pricing from '@/app/pricing/page';
import FAQ from '@/app/faq/page';
import Contact from '@/app/contact/page';
import Privacy from '@/app/privacy/page';
import Empresas from '@/app/empresas/page';
import Pymes from '@/app/soluciones-para-pymes/page';
import Servicios from '@/app/servicios/page';
import Soluciones from '@/app/soluciones/page';
import SoftwareEmpresarial from '@/app/servicios/software-empresarial/page';
import Automatizacion from '@/app/servicios/automatizacion-e-integraciones/page';
import { Navbar } from '@/components/blocks/navbar';
import { Footer } from '@/components/blocks/footer';
import { FloatingWhatsApp } from '@/components/floating-whatsapp';
import { ThemeProvider } from '@/components/theme-provider';
import '@fontsource/inter/latin.css';
import '@/styles/globals.css';
import './fonts.css';

const root = document.getElementById('landing-root')!;
const pages = { home: Home, about: About, pricing: Pricing, faq: FAQ, contact: Contact, privacy: Privacy, empresas: Empresas, pymes: Pymes, servicios: Servicios, soluciones: Soluciones, softwareEmpresarial: SoftwareEmpresarial, automatizacion: Automatizacion };
const Page = pages[root.dataset.page as keyof typeof pages] ?? Home;
createRoot(root).render(
    <ThemeProvider attribute="class" defaultTheme="dark" enableSystem={false} disableTransitionOnChange>
        <Navbar />
        <main><Page /></main>
        <Footer />
        <FloatingWhatsApp />
    </ThemeProvider>
);
