import { Check } from "lucide-react";
import Link from "next/link";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { cn } from "@/lib/utils";

const plans = [
  { name: "Datos & BI", description: "Información útil para operar y decidir.", features: ["Arquitectura de datos", "Bases de datos empresariales", "Dashboards y reportería", "ETL y consolidación"] },
  { name: "Modernización", description: "Evoluciona sistemas y procesos que ya no responden a tu operación.", features: ["Migración de software", "Reingeniería de procesos", "Reemplazo de Excel", "Integración legacy"] },
  { name: "Infraestructura", description: "Sistemas disponibles, seguros y listos para crecer.", features: ["Linux, Nginx y VPS", "CI/CD y despliegues", "SSL, respaldos y monitoreo", "Staging y producción"] },
];

export const Pricing = ({ className }: { className?: string }) => (
  <section className={cn("py-28 lg:py-32", className)}>
    <div className="container max-w-5xl">
      <div className="space-y-4 text-center"><h2 className="text-2xl tracking-tight md:text-4xl lg:text-5xl">Soluciones empresariales que escalan contigo</h2><p className="text-muted-foreground mx-auto max-w-xl leading-snug text-balance">No vendemos paquetes ni tecnología por tecnología. Diseñamos el alcance correcto para resolver el problema de tu empresa.</p></div>
      <div className="mt-8 grid items-start gap-5 text-start md:mt-12 md:grid-cols-3 lg:mt-20">{plans.map((plan) => <Card key={plan.name} className={plan.name === "Modernización" ? "outline-primary origin-top outline-4" : ""}><CardContent className="flex flex-col gap-7 px-6 py-5"><div className="space-y-2"><h3 className="text-foreground font-semibold">{plan.name}</h3><p className="text-muted-foreground text-sm leading-snug">{plan.description}</p></div><div className="space-y-3">{plan.features.map((feature) => <div key={feature} className="text-muted-foreground flex items-center gap-1.5"><Check className="size-5 shrink-0" /><span className="text-sm">{feature}</span></div>)}</div><Button className="w-fit" variant={plan.name === "Modernización" ? "default" : "outline"} asChild><Link href="/contact">Ver solución</Link></Button></CardContent></Card>)}</div>
    </div>
  </section>
);
