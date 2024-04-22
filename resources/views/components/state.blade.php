@if( $getState() === 1 )
    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ring-yellow-600/20"> <i class="text-2xs fa fa-circle text-orange-500 pr-2 animate-pulse"> </i> Pendiente de aprobación</span>
@elseif( $getState() === 2 )
    <span class="inline-flex items-center rounded-md  px-2 py-1 text-xs font-medium ring-1 ring-inset ring-green-600/20"><i class="text-2xs fa fa-circle text-green-500 pr-2"> </i> Aprobado</span>
@elseif( $getState() === 3 )
    <span class="inline-flex items-center rounded-md  px-2 py-1 text-xs font-medium ring-1 ring-inset ring-green-600/20"><i class="text-2xs fa fa-circle text-red-500 pr-2"> </i> Rechazado</span>
@elseif( $getState() === 4 )
    <span class="inline-flex items-center rounded-md  px-2 py-1 text-xs font-medium ring-1 ring-inset ring-green-600/20"><i class="text-2xs fa fa-circle text-blue-500 pr-2"> </i> En asignación de precio</span>
@elseif( $getState() === 5 )
    <span class="inline-flex items-center rounded-md  px-2 py-1 text-xs font-medium ring-1 ring-inset ring-green-600/20"><i class="text-2xs fa fa-circle text-blue-500 pr-2"> </i> En orden de compra</span>
@elseif( $getState() === 6 )
    <span class="inline-flex items-center rounded-md  px-2 py-1 text-xs font-medium ring-1 ring-inset ring-green-600/20"><i class="text-2xs fa fa-circle text-red-700-500 pr-2"> </i> Anulada</span>
@endif
