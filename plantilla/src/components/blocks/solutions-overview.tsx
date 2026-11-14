import Link from "next/link";
import { ArrowRight } from "lucide-react";

import { Background } from "@/components/background";
import { Button } from "@/components/ui/button";

const paths = [
  ["01", "Conectar", "Sistemas, datos y equipos que hoy operan por separado."],
  ["02", "Automatizar", "Tareas repetitivas que consumen tiempo y generan errores."],
  ["03", "Evolucionar", "Software e infraestructura que necesita una nueva etapa."],
];

export function SolutionsOverview() {
  return <Background><section className="container py-28 lg:pt-44 lg:pb-32"><div className="mx-auto max-w-4xl text-center"><p className="text-primary font-mono text-xs font-bold tracking-[.16em]">SOLUCIONES NEXUS</p><h1 className="mt-6 text-4xl tracking-tight md:text-6xl lg:text-7xl">La solución correcta depende de dónde está la fricción.</h1><p className="text-muted-foreground mx-auto mt-7 max-w-2xl text-lg leading-relaxed">No empezamos con un paquete. Empezamos entendiendo qué necesita cambiar en tu operación.</p></div><div className="mt-20 border-y">{paths.map(([number, title, description]) => <div key={number} className="grid gap-4 border-b py-8 last:border-b-0 md:grid-cols-[7rem_1fr_1.2fr] md:items-baseline md:py-12"><span className="text-primary font-mono text-sm">{number}</span><h2 className="text-3xl tracking-tight md:text-5xl">{title}</h2><p className="text-muted-foreground max-w-md leading-relaxed">{description}</p></div>)}</div><div className="mt-12 flex justify-center"><Button size="lg" asChild><Link href="/contact">Explorar mi solución <ArrowRight /></Link></Button></div></section></Background>;
}
