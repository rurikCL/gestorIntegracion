<?php

namespace App\Http\Controllers\ApiProd;

use App\Http\Controllers\Controller;
use App\Models\FLU\FLU_Homologacion;
use App\Models\MA\MA_Marcas;
use App\Models\MA\MA_Modelos;
use Illuminate\Http\Request;

class FLujoHomologacionController extends Controller
{
    public function insertaMasivo()
    {
        $data = json_decode('
        [
    {
        "title": "AUDI",
        "modelos": [
            "A1",
            "A4"
        ]
    },
    {
        "title": "BAIC",
        "modelos": [
            "BJ40P",
            "X25",
            "X35",
            "X55"
        ]
    },
    {
        "title": "BMW",
        "modelos": [
            "116",
            "118",
            "118I",
            "218I",
            "220",
            "220I",
            "320I LCI",
            "328",
            "330I",
            "420I CABRIOLET",
            "420I COUPÉ",
            "420I GRAN COUPÉ",
            "520D",
            "740I",
            "M850I CABRIOLET",
            "M850I COUPÉ",
            "M850I GRAN COUPÉ",
            "X1",
            "X2",
            "X3",
            "X4",
            "X5",
            "X6",
            "X7"
        ]
    },
    {
        "title": "BRILLIANCE",
        "modelos": [
            "H2L",
            "KONECT",
            "T30",
            "T32",
            "T50",
            "T52",
            "V3",
            "X30"
        ]
    },
    {
        "title": "BYD",
        "modelos": [
            "DOLPHIN",
            "HAN EV",
            "SONG PLUS DM-I",
            "TANG EV",
            "YUAN PLUS EV"
        ]
    },
    {
        "title": "CHANGAN",
        "modelos": [
            "ALSVIN",
            "CS15",
            "CS35",
            "CS35 PLUS",
            "CS55",
            "CS75",
            "CX70",
            "HUNTER",
            "MD201",
            "MS201",
            "UNI-T"
        ]
    },
    {
        "title": "CHERY",
        "modelos": [
            "ARRIZO 5",
            "GRAND TIGGO",
            "K60",
            "TIGGO",
            "TIGGO 2",
            "TIGGO 2 PRO",
            "TIGGO 3",
            "TIGGO 3 PRO",
            "TIGGO 7 PRO",
            "TIGGO 8",
            "TIGGO 8 PRO"
        ]
    },
    {
        "title": "CHEVROLET",
        "modelos": [
            "BLAZER",
            "CAMARO",
            "CAPTIVA",
            "COLORADO",
            "CRUZE",
            "D-MAX",
            "FVR 1724",
            "GROOVE",
            "MONTANA",
            "N300",
            "N400",
            "N400 MAX",
            "ONIX",
            "ONIX HATCHBACK",
            "ONIX SEDAN",
            "PRISMA",
            "PRISMA ",
            "SAIL",
            "SILVERADO",
            "SONIC",
            "SPARK",
            "SPARK GT",
            "SPIN",
            "SUBURBAN",
            "TAHOE",
            "TRACKER",
            "TRAILBLAZER",
            "TRAVERSE"
        ]
    },
    {
        "title": "CITROEN",
        "modelos": [
            "BERLINGO",
            "C-3",
            "C-4",
            "C-5",
            "C-ELYSEE",
            "C3",
            "C3 AIRCROSS",
            "C4",
            "C5 AIRCROSS",
            "DS3",
            "DS4",
            "NUEVO C3",
            "SPACETOURER"
        ]
    },
    {
        "title": "CUPRA",
        "modelos": [
            "FORMENTOR"
        ]
    },
    {
        "title": "DFM",
        "modelos": [
            "JOYEAR"
        ]
    },
    {
        "title": "DFSK",
        "modelos": [
            "560",
            "580",
            "CARGO",
            "CARGO BOX",
            "CARGO VAN",
            "SUV 500",
            "SUV 560",
            "SUV 580",
            "TRUCK CABINA DOBLE",
            "TRUCK CABINA SIMPLE"
        ]
    },
    {
        "title": "DODGE",
        "modelos": [
            "JOURNEY"
        ]
    },
    {
        "title": "DONGFENG",
        "modelos": [
            "AX4",
            "JOYEAR X3"
        ]
    },
    {
        "title": "DS",
        "modelos": [
            "DS 3",
            "DS7 CROSSBACK"
        ]
    },
    {
        "title": "EXEED",
        "modelos": [
            "LX",
            "TXL",
            "VX"
        ]
    },
    {
        "title": "FIAT",
        "modelos": [
            "500",
            "500X",
            "ARGO",
            "CRONOS",
            "PULSE",
            "QUBO",
            "STRADA",
            "UNO"
        ]
    },
    {
        "title": "FORD",
        "modelos": [
            "BRONCO SPORT",
            "ECOSPORT",
            "EDGE",
            "ESCAPE",
            "EXPEDITION",
            "EXPLORER",
            "F-150",
            "FIESTA",
            "FUSION",
            "MAVERICK",
            "MUSTANG",
            "RANGER",
            "TERRITORY",
            "TRANSIT"
        ]
    },
    {
        "title": "GAC MOTORS",
        "modelos": [
            "GA4",
            "GS3"
        ]
    },
    {
        "title": "GEELY",
        "modelos": [
            "AZKARRA",
            "COOLRAY"
        ]
    },
    {
        "title": "GREAT WALL",
        "modelos": [
            "H6",
            "M4",
            "POER",
            "POER PLUS AUTOMÁTICA",
            "WINGLE 5",
            "WINGLE 5 DIÉSEL",
            "WINGLE 7",
            "WINGLE 7 DIÉSEL"
        ]
    },
    {
        "title": "HAVAL",
        "modelos": [
            "DARGO",
            "H2",
            "H6",
            "JOLION"
        ]
    },
    {
        "title": "HONDA",
        "modelos": [
            "CITY",
            "CIVIC",
            "CR-V",
            "FIT",
            "HR-V",
            "PILOT",
            "RIDGELINE",
            "WR-V"
        ]
    },
    {
        "title": "HYUNDAI",
        "modelos": [
            "ACCENT",
            "ATOS",
            "CRETA",
            "CRETA GRAND",
            "ELANTRA",
            "GRAND I10",
            "GRAND I10 SEDAN",
            "GRAND SANTA FE",
            "I-30",
            "I20",
            "I30",
            "NEW KONA ELÉCTRICO",
            "NEW KONA HÍBRIDO",
            "NEW TUCSON HÍBRIDO",
            "PALISADE",
            "SANTA FE",
            "STARIA",
            "TUCSON",
            "VELOSTER",
            "VENUE",
            "VERNA"
        ]
    },
    {
        "title": "JAC",
        "modelos": [
            "JS2",
            "JS3",
            "JS4",
            "JS8",
            "REFINE",
            "S2",
            "S3",
            "SUNRAY",
            "T6",
            "T8",
            "T8 PRO",
            "X200"
        ]
    },
    {
        "title": "JEEP",
        "modelos": [
            "COMPASS",
            "GRAND CHEROKEE",
            "RENEGADE",
            "WRANGLER"
        ]
    },
    {
        "title": "JETOUR",
        "modelos": [
            "X70",
            "X70 PLUS"
        ]
    },
    {
        "title": "KAIYI",
        "modelos": [
            "KYX3",
            "KYX3 PRO"
        ]
    },
    {
        "title": "KARRY",
        "modelos": [
            "Q22 CABINA DOBLE",
            "Q22 CABINA SIMPLE",
            "Q22 CARGO BOX"
        ]
    },
    {
        "title": "KIA",
        "modelos": [
            "CARENS",
            "CARNIVAL",
            "CERATO",
            "CERATO 5",
            "FRONTIER",
            "GRAND CARNIVAL",
            "MOHAVE",
            "MORNING",
            "NIRO",
            "RIO",
            "RIO 4",
            "RIO 5",
            "SELTOS",
            "SOLUTO",
            "SONET",
            "SORENTO",
            "SOUL",
            "SPORTAGE",
            "STINGER"
        ]
    },
    {
        "title": "KYC",
        "modelos": [
            "X5"
        ]
    },
    {
        "title": "LAND ROVER",
        "modelos": [
            "EVOQUE"
        ]
    },
    {
        "title": "LEXUS",
        "modelos": [
            "IS 350"
        ]
    },
    {
        "title": "MAHINDRA",
        "modelos": [
            "PIK UP",
            "PIKUP",
            "XUV",
            "XUV 500",
            "XUV500"
        ]
    },
    {
        "title": "MAXUS",
        "modelos": [
            "C35",
            "DELIVER9",
            "ET90",
            "EUNIQ6",
            "G10 CARGO",
            "G10 CARGO D20",
            "T60",
            "T60 D20",
            "T90"
        ]
    },
    {
        "title": "MAZDA",
        "modelos": [
            "2 SEDAN",
            "2 SPORT",
            "3 SEDAN",
            "3 SPORT",
            "5",
            "6",
            "ALL NEW BT-50",
            "BT-50",
            "CX-3",
            "CX-30",
            "CX-5",
            "CX-5 ",
            "CX-9",
            "MX-5"
        ]
    },
    {
        "title": "MERCEDES BENZ",
        "modelos": [
            "GLA 200",
            "GLC 220"
        ]
    },
    {
        "title": "MERCEDES-BENZ",
        "modelos": [
            "180",
            "C 220",
            "E 250",
            "GLA 220 D"
        ]
    },
    {
        "title": "MG",
        "modelos": [
            "3",
            "5",
            "6",
            "GS",
            "GT",
            "HS",
            "MARVEL R",
            "MG3",
            "MG6",
            "MG6 ",
            "NEW ZX",
            "ONE",
            "RX5",
            "ZS",
            "ZS EV",
            "ZX"
        ]
    },
    {
        "title": "MINI",
        "modelos": [
            "COOPER",
            "COOPER S",
            "COOPER SE",
            "COUNTRYMAN COOPER S",
            "F56"
        ]
    },
    {
        "title": "MITSUBISHI",
        "modelos": [
            "ASX",
            "ECLIPSE",
            "ECLIPSE CROSS",
            "L200",
            "MIRAGE",
            "MONTERO",
            "OUTLANDER"
        ]
    },
    {
        "title": "NISSAN",
        "modelos": [
            "JUKE",
            "KICKS",
            "LEAF",
            "MARCH",
            "MURANO",
            "NAVARA",
            "NOTE",
            "NP300",
            "NV350",
            "PATHFINDER",
            "QASHQAI",
            "SENTRA",
            "VERSA",
            "X-TRAIL"
        ]
    },
    {
        "title": "OPEL",
        "modelos": [
            "COMBO",
            "CORSA",
            "CROSSLAND",
            "CROSSLAND X",
            "GRANDLAND",
            "GRANDLAND ",
            "GRANDLAND X",
            "MOKKA",
            "VIVARO"
        ]
    },
    {
        "title": "PEUGEOT",
        "modelos": [
            "2008",
            "208",
            "3008",
            "301",
            "308",
            "5008",
            "BOXER",
            "E-2008",
            "EXPERT",
            "LANDTREK",
            "PARTNER",
            "RIFTER",
            "TRAVELLER"
        ]
    },
    {
        "title": "RAM",
        "modelos": [
            "1000",
            "700",
            "V700",
            "V700 RAPID"
        ]
    },
    {
        "title": "RENAULT",
        "modelos": [
            "ARKANA",
            "CAPTUR",
            "CLIO",
            "CLIO IV",
            "DOKKER",
            "DUSTER",
            "EXPRESS",
            "KOLEOS",
            "KWID",
            "NEW DUSTER",
            "OROCH",
            "SYMBOL"
        ]
    },
    {
        "title": "SEAT",
        "modelos": [
            "ARONA",
            "ATECA",
            "IBIZA",
            "LEON",
            "TARRACO"
        ]
    },
    {
        "title": "SHINERAY",
        "modelos": [
            "G05"
        ]
    },
    {
        "title": "SKODA",
        "modelos": [
            "FABIA",
            "KAROQ",
            "OCTAVIA",
            "RAPID"
        ]
    },
    {
        "title": "SSANGYONG",
        "modelos": [
            "ACTYON",
            "ACTYON SPORT",
            "KORANDO",
            "MUSSO",
            "REXTON",
            "STAVIC"
        ]
    },
    {
        "title": "SUBARU",
        "modelos": [
            "ALL NEW WRX",
            "ALL NEW WRX SPORTWAGON",
            "EVOLTIS",
            "FORESTER",
            "FORESTER HÍBRIDO",
            "IMPREZA",
            "IMPREZA SEDAN",
            "LEGACY",
            "NEW XV HÍBRIDO",
            "OUTBACK",
            "WRX",
            "XV"
        ]
    },
    {
        "title": "SUZUKI",
        "modelos": [
            "ALTO",
            "BALENO",
            "CARRY",
            "CELERIO",
            "CIAZ",
            "DZIRE",
            "ERTIGA",
            "GRAND NOMADE",
            "GRAND VITARA",
            "JIMNY",
            "S CROSS",
            "S-CROSS",
            "S-PRESSO",
            "SWIFT",
            "VITARA",
            "XL7"
        ]
    },
    {
        "title": "TOYOTA",
        "modelos": [
            "4RUNNER",
            "C-HR",
            "COROLLA",
            "COROLLA CROSS",
            "FORTUNER",
            "HIACE",
            "HILUX",
            "LAND CRUISER PRADO",
            "RAIZE",
            "RAV4",
            "RUSH",
            "YARIS",
            "YARIS SEDAN"
        ]
    },
    {
        "title": "VOLKSWAGEN",
        "modelos": [
            "AMAROK",
            "ATLAS",
            "GOL",
            "GOLF",
            "JETTA",
            "NIVUS",
            "POLO",
            "SAVEIRO",
            "T-CROSS",
            "TAOS",
            "TIGUAN",
            "VIRTUS",
            "VOYAGE"
        ]
    },
    {
        "title": "VOLVO",
        "modelos": [
            "S60",
            "V40"
        ]
    },
    {
        "title": "ZX AUTO",
        "modelos": [
            "TERRALORD"
        ]
    }
]
        ');

        $indiceMarcas = "title";
        $indiceModelos = "modelos";
        $idFlujo = 4;

        foreach ($data as $row) {
            $marca = $row->$indiceMarcas;
            $marcaPompeyo = MA_Marcas::where('Marca', 'like', $marca)
                ->first();
            $homologacion = FLU_Homologacion::firstOrCreate(
                [
                    'FlujoID' => $idFlujo,
                    'ValorIdentificador' => $marca,
                    'CodHomologacion' => "marca"
                ],
                [
                    'FechaCreacion' => date("Y-m-d H:i:s"),
                    'EventoCreacionID' => 1,
                    'UsuarioCreacionID' => 1,
                    'CodHomologacion' => "marca",
                    'FlujoID' => $idFlujo,
                    'ValorIdentificador' => $marca,
                    'ValorNombre' => $marcaPompeyo->Marca ?? '',
                    'ValorRespuesta' => $marcaPompeyo->ID ?? 1,
                    'Activo' => 1,
                ]
            );
            echo "Insertado marca : " . $homologacion->ValorIdentificador.PHP_EOL;

            foreach ($row->$indiceModelos as $modelo) {
                $modeloPompeyo = MA_Modelos::where('Modelo', 'like', $modelo)
                    ->first();
                $homologacion = FLU_Homologacion::firstOrCreate(
                    [
                        'FlujoID' => $idFlujo,
                        'ValorIdentificador' => $modelo,
                        'CodHomologacion' => "modelo"
                    ],
                    [
                        'FechaCreacion' => date("Y-m-d H:i:s"),
                        'EventoCreacionID' => 1,
                        'UsuarioCreacionID' => 1,
                        'CodHomologacion' => "modelo",
                        'FlujoID' => $idFlujo,
                        'ValorIdentificador' => $modelo,
                        'ValorNombre' => $modeloPompeyo->Modelo ?? '',
                        'ValorRespuesta' => $modeloPompeyo->ID ?? 1,
                        'Activo' => 1,
                    ]
                );
                echo "Insertado modelo : " . $homologacion->ValorIdentificador.PHP_EOL;

            }

        }

        return "success";
    }
}
