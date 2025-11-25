<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Promocion;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PromocionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $productos = Producto::all();
            $categorias = Categoria::all();

            if ($productos->isEmpty() || $categorias->isEmpty()) {
                $this->command->warn('⚠️  No hay productos o categorías para crear promociones.');

                return;
            }

            // Promoción 1: Descuento porcentual en bebidas - VIGENTE
            $promo1 = Promocion::create([
                'nombre' => 'Happy Hour - 20% en Bebidas',
                'tipo' => 'porcentaje',
                'aplica_sobre' => 'item',
                'valor' => 20.00,
                'tope_descuento' => 50.00,
                'fecha_inicio' => Carbon::now()->subDays(10),
                'fecha_fin' => Carbon::now()->addDays(20),
                'hora_inicio' => '17:00',
                'hora_fin' => '20:00',
                'dias_semana' => ['lun', 'mar', 'mie', 'jue', 'vie'],
                'prioridad' => 5,
                'activo' => true,
            ]);

            // Relacionar con categoría de bebidas
            $categoriaBebidas = $categorias->where('nombre', 'like', '%bebida%')->first();
            if ($categoriaBebidas) {
                DB::table('promocion_productos')->insert([
                    'promocion_id' => $promo1->id,
                    'producto_id' => null,
                    'categoria_id' => $categoriaBebidas->id,
                    'cantidad_requerida' => 1,
                ]);
            }

            // Promoción 2: 2x1 en productos específicos - VIGENTE
            $promo2 = Promocion::create([
                'nombre' => '2x1 en Café del Día',
                'tipo' => '2x1',
                'aplica_sobre' => 'item',
                'valor' => 50.00, // 50% de descuento
                'fecha_inicio' => Carbon::now()->subDays(5),
                'fecha_fin' => Carbon::now()->addDays(25),
                'hora_inicio' => '08:00',
                'hora_fin' => '11:00',
                'dias_semana' => ['lun', 'mar', 'mie', 'jue', 'vie', 'sab', 'dom'],
                'prioridad' => 8,
                'activo' => true,
            ]);

            // Relacionar con primer producto
            if ($productos->count() > 0) {
                DB::table('promocion_productos')->insert([
                    'promocion_id' => $promo2->id,
                    'producto_id' => $productos->first()->id,
                    'categoria_id' => null,
                    'cantidad_requerida' => 2,
                ]);
            }

            // Promoción 3: Monto fijo de descuento - VIGENTE
            $promo3 = Promocion::create([
                'nombre' => 'Descuento Fin de Semana - Bs. 15',
                'tipo' => 'monto_fijo',
                'aplica_sobre' => 'item',
                'valor' => 15.00,
                'fecha_inicio' => Carbon::now()->subDays(3),
                'fecha_fin' => Carbon::now()->addDays(30),
                'hora_inicio' => null,
                'hora_fin' => null,
                'dias_semana' => ['sab', 'dom'],
                'prioridad' => 3,
                'activo' => true,
            ]);

            // Relacionar con categoría de postres
            $categoriaPostres = $categorias->where('nombre', 'like', '%postre%')->first()
                             ?? $categorias->skip(1)->first();
            if ($categoriaPostres) {
                DB::table('promocion_productos')->insert([
                    'promocion_id' => $promo3->id,
                    'producto_id' => null,
                    'categoria_id' => $categoriaPostres->id,
                    'cantidad_requerida' => 1,
                ]);
            }

            // Promoción 4: 2x1 en Empanadas - VENCIDA (Cambio de 3x2 a 2x1 por limitación de enum)
            $promo4 = Promocion::create([
                'nombre' => '2x1 en Empanadas - VENCIDA',
                'tipo' => '2x1',
                'aplica_sobre' => 'item',
                'valor' => 50.00,
                'fecha_inicio' => Carbon::now()->subDays(60),
                'fecha_fin' => Carbon::now()->subDays(5),
                'hora_inicio' => null,
                'hora_fin' => null,
                'dias_semana' => [],
                'prioridad' => 4,
                'activo' => false,
            ]);

            if ($productos->count() > 2) {
                DB::table('promocion_productos')->insert([
                    'promocion_id' => $promo4->id,
                    'producto_id' => $productos->skip(2)->first()->id,
                    'categoria_id' => null,
                    'cantidad_requerida' => 3,
                ]);
            }

            // Promoción 5: Descuento especial - FUTURA
            $promo5 = Promocion::create([
                'nombre' => 'Gran Promoción de Verano - 30%',
                'tipo' => 'porcentaje',
                'aplica_sobre' => 'item',
                'valor' => 30.00,
                'tope_descuento' => 100.00,
                'fecha_inicio' => Carbon::now()->addDays(10),
                'fecha_fin' => Carbon::now()->addDays(40),
                'hora_inicio' => null,
                'hora_fin' => null,
                'dias_semana' => [],
                'prioridad' => 10,
                'activo' => true,
            ]);

            if ($categorias->count() > 0) {
                DB::table('promocion_productos')->insert([
                    'promocion_id' => $promo5->id,
                    'producto_id' => null,
                    'categoria_id' => $categorias->first()->id,
                    'cantidad_requerida' => 1,
                ]);
            }

            // Promoción 6: Descuento inactivo
            $promo6 = Promocion::create([
                'nombre' => 'Promo Desactivada - 15%',
                'tipo' => 'porcentaje',
                'aplica_sobre' => 'item',
                'valor' => 15.00,
                'fecha_inicio' => Carbon::now()->subDays(2),
                'fecha_fin' => Carbon::now()->addDays(15),
                'hora_inicio' => null,
                'hora_fin' => null,
                'dias_semana' => [],
                'prioridad' => 2,
                'activo' => false,
            ]);

            if ($productos->count() > 1) {
                DB::table('promocion_productos')->insert([
                    'promocion_id' => $promo6->id,
                    'producto_id' => $productos->skip(1)->first()->id,
                    'categoria_id' => null,
                    'cantidad_requerida' => 1,
                ]);
            }

            $this->command->info('✅ Promociones creadas exitosamente (6 promociones).');
        } catch (\Exception $e) {
            $this->command->error('Error al crear promociones: '.$e->getMessage());
            $this->command->error($e->getTraceAsString());
        }
    }
}
