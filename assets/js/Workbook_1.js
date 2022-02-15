//Classes
class Inner {
    constructor (Quantity, Material, Gage, ThicknessB, ThicknessC, Pattern,
        Holes, Centers, Diameter, OA, is_OD,
        Strip, Length, Setup, Crating, TPC,
        Shop, Electric, Men, CutSpd, TagCost,
        Density, Scrap, Stamping, MatCost, Gas,
        Labor, TPB, Blade, WS, Markup,
        Overhead, Basis, Unit) {

    //Additional Cells
    var B68 = 0; 

    // Unit indicator: 0 - inch, 1 - foot
    const FoottoInch = 12;
    if (Unit) {
        Unit = 12;
    } else {
        Unit = 1;
    }
    var OA2 = 0;
    if (Centers > 0) {
        OA2 = (Holes*Holes)/(Centers*Centers) * OA/100;
    }
    var ID, OD;
    if (is_OD) {
        ID = Diameter + 2*ThicknessB;
        OD = Diameter;
    } else {
        ID = Diameter;
        OD = Diameter + 2*ThicknessC;
    }
    var Crate = Math.ceil (Quantity/TPC);
    if (TPC == 0) {
        Crate = 0;
    }
    var Crating2 = Crating * Crate;
    var Angle = 90 - Math.acos (Strip/Math.PI/OD) * 180/Math.PI;
    var HPSI = 0;
    if (Holes > 0) {
        HPSI = OA2 * (100/(78.54*Holes*Holes));
    }
    
    //Per tube inch column
    var OrderInfo = [];
    //Linear ft. material
    OrderInfo[0] = Math.PI * OD / Strip / FoottoInch;
    //Linear ft. material (w/ scrap)
    OrderInfo[1] = OrderInfo[0] * (1 + Scrap);
    //Weight (not perfed.)
    OrderInfo[2] = ThicknessB * Strip * Density * OrderInfo[0] * FoottoInch;
    //Weight (not perfed. w/scrap)
    OrderInfo[3] = OrderInfo[2] * (1 + Scrap);
    //Weight (perfed.)
    OrderInfo[4] = OrderInfo[2] * (1 - OA2);
    //Weight (perfed. w/scrap)
    OrderInfo[5] = OrderInfo[4] * (1 + Scrap);
    //Number of holes
    OrderInfo[6] = HPSI * Strip * OrderInfo[0]/FoottoInch;
    //Welding time (min)
    OrderInfo[7] = OrderInfo[0]/ WS * 12 * 6/5;
    //Cutoff Time (min)
    OrderInfo[8] = (OD/CutSpd*2 + 1)/Length;
    //Total Time (hours)
    OrderInfo[9] = (OrderInfo[7] + OrderInfo[8])/60;

    //Per tube inch column
    var Costs = [];
    //Crating
    Costs[0] = Crating2/Quantity/Length;
    //Tag
    Costs[1] = TagCost/Length;
    //Shop supplies
    Costs[2] = Shop / FoottoInch;
    //Electric cost
    Costs[3] = OrderInfo[7] * Electric;
    //Blade cost
    Costs[4] = Blade/TPB/Length;
    //Stamping cost
    Costs[5] = Stamping*OrderInfo[1];
    //Material Cost
    Costs[6] = MatCost*OrderInfo[3];
    //Gas Cost
    Costs[7] = Gas*OrderInfo[7]/60;

    //Per tube inch column
    var Costs2 = [];

    //Steel Cost
    Costs2[0] = Costs.reduce ((a, b) => a+b, 0) - Costs[7];
    //Cutoff Labor Cost
    Costs2[1] = (OrderInfo[8]*Length*(3/4))/144;
    //Welding Labor cost
    Costs2[2] = Men*Labor*OrderInfo[7]/60;
    //Supply Cost
    Costs2[3] = Costs[0] + Costs[1] + Costs[2] + Costs[3] + Costs[4] + Costs[7];

    var Costs3 = [];
    if (MatCost > 0) {
        //Marginal Cost
        Costs3[0] = Costs2.reduce ((a, b) => a+b, 0) + Setup/(Length * Quantity);
        //Overhead
        Costs3[1] = OrderInfo[3]/Basis*Overhead;
    }
    //Markups
    Costs3[2] = (Costs3[0] + Costs3[1])*Markup;
    var TotalCost = 0;
    if (MatCost > 0) {
        TotalCost = Costs3.reduce ((a, b) => a+b, 0) + B68;
    }

    this.OD = OD;
    this.ID = ID;
    this.Costs = Costs;
    this.Costs2 = Costs2;
    this.Costs3 = Costs3;
    this.OrderInfo = OrderInfo;
    this.TotalCost = TotalCost;
    this.Length = Length;
    this.Angle = Angle;
    this.Quantity = Quantity;
    this.Unit = Unit;
    this.Crating2 = Crating2;
    this.Crate = Crate;
    this.OA2 = OA2;
    // this.HPSI;
    }
}

class FilterInner {
    constructor (Quantity, Material, Gage, Thickness, Pattern,
            Holes, Centers, Diameter, OA, is_OD,
            Strip, Length, Setup, Crating, TPC, Crate,
            Shop, Electric, Men, CutSpd, TagCost,
            Density, Scrap, Stamping, MatCost, Gas,
            Labor, TPB, Blade, WS, Markup,
            Overhead, Basis, Unit) {

    //Additional Cells
    var B68 = 0; 

    // Unit indicator: 0 - inch, 1 - foot
    const FoottoInch = 12;
    if (Unit) {
        Unit = 12;
    } else {
        Unit = 1;
    }
    var OA2 = 0;
    if (Centers > 0) {
        OA2 = (Holes*Holes)/(Centers*Centers) * OA/100;
    }
    var ID = Diameter;
    var OD = Diameter + 2*Thickness;

    var Crating2 = Crating * Crate;
    var Angle = 90 - Math.acos (Strip/Math.PI/OD) * 180/Math.PI;
    var HPSI = 0;
    if (Holes > 0) {
        HPSI = OA2 * (100/(78.54*Holes*Holes));
    }

    //Per tube inch column
    var OrderInfo = [];
    //Linear ft. material
    OrderInfo[0] = Math.PI * OD / Strip / FoottoInch;
    //Linear ft. material (w/ scrap)
    OrderInfo[1] = OrderInfo[0] * (1 + Scrap);
    //Weight (not perfed.)
    OrderInfo[2] = Thickness * Strip * Density * OrderInfo[0] * FoottoInch;
    //Weight (not perfed. w/scrap)
    OrderInfo[3] = OrderInfo[2] * (1 + Scrap);
    //Weight (perfed.)
    OrderInfo[4] = OrderInfo[2] * (1 - OA2);
    //Weight (perfed. w/scrap)
    OrderInfo[5] = OrderInfo[4] * (1 + Scrap);
    //Number of holes
    OrderInfo[6] = HPSI * Strip * OrderInfo[0]/FoottoInch;
    //Welding time (min)
    OrderInfo[7] = OrderInfo[0]/ WS * 12 * 6/5;
    //Cutoff Time (min)
    OrderInfo[8] = (OD/CutSpd*2*6/5)/Length;
    //Total Time (hours)
    OrderInfo[9] = (OrderInfo[7] + OrderInfo[8])/60;

    //Per tube inch column
    var Costs = [];
    //Crating
    Costs[0] = Crating2/Quantity/Length;
    //Tag
    Costs[1] = TagCost/Length;
    //Shop supplies
    Costs[2] = Shop / FoottoInch;
    //Electric cost
    Costs[3] = OrderInfo[7] * Electric;
    //Blade cost
    Costs[4] = Blade/TPB/Length;
    //Stamping cost
    Costs[5] = Stamping*OrderInfo[1];
    //Material Cost
    Costs[6] = MatCost*OrderInfo[1];
    //Gas Cost
    Costs[7] = Gas*OrderInfo[7]/60;

    //Per tube inch column
    var Costs2 = [];

    //Steel Cost
    var Multiplier = 1.03;
    Costs2[0] = Costs[5] + Costs[6]*Multiplier;
    //Cutoff Labor Cost
    Costs2[1] = OrderInfo[8]*Labor/60;
    //Welding Labor cost
    Costs2[2] = Men*Labor*OrderInfo[7]/60;
    //Supply Cost
    Costs2[3] = Costs[0] + Costs[1] + Costs[2] + Costs[3] + Costs[4] + Costs[7];

    var Costs3 = [];

    //Marginal Cost
    Costs3[0] = Costs2.reduce ((a, b) => a+b, 0) + Setup/(Length * Quantity);
    //Overhead
    //Costs3[1] = OrderInfo[3]/Basis*Overhead;
    //Markup
    Costs3[1] = Costs3[0]*Markup;
    var TotalCost = Costs3[0] + Costs3[1] + B68;

    this.ID = ID;
    this.OD = OD;
    this.Costs = Costs;
    this.Costs2 = Costs2;
    this.Costs3 = Costs3;
    this.OrderInfo = OrderInfo;
    this.TotalCost = TotalCost;
    this.Length = Length;
    this.Angle = Angle;
    this.Quantity = Quantity;
    this.Unit = Unit;
    this.Crate = Crate;
    this.Crating2 = Crating2;
    this.OA2 = OA2;
    }
}

class FilterOuter {
    constructor (Quantity, Material, Gage, Thickness, Pattern,
            Holes, Centers, Diameter, OA, is_OD,
            Strip, Length, Setup, Crating, TPC,
            Shop, Electric, Men, CutSpd, TagCost,
            Density, Scrap, Stamping, MatCost, Gas,
            Labor, TPB, Blade, WS, Markup,
            Overhead, Basis, Unit) {

    //Additional Cells
    var B68 = 0; 

    // Unit indicator: 0 - inch, 1 - foot
    const FoottoInch = 12;
    if (Unit) {
        Unit = 12;
    } else {
        Unit = 1;
    }
    var OA2 = 0;
    if (Centers > 0) {
        OA2 = (Holes*Holes)/(Centers*Centers) * OA/100;
    }
    var ID = Diameter;
    var OD = Diameter + 2*Thickness;
    var Crate = Math.ceil (Quantity/TPC);
    if (TPC == 0) {
        Crate = 0;
    }
    var Crating2 = Crating * Crate;
    var Angle = 90 - Math.acos (Strip/Math.PI/OD) * 180/Math.PI;
    var HPSI = 0;
    if (Holes > 0) {
        HPSI = OA2 * (100/(78.54*Holes*Holes));
    }

    //Per tube inch column
    var OrderInfo = [];
    //Linear ft. material
    OrderInfo[0] = Math.PI * OD / Strip / FoottoInch;
    //Linear ft. material (w/ scrap)
    OrderInfo[1] = OrderInfo[0] * (1 + Scrap);
    //Weight (not perfed.)
    OrderInfo[2] = Thickness * Strip * Density * OrderInfo[0] * FoottoInch;
    //Weight (not perfed. w/scrap)
    OrderInfo[3] = OrderInfo[2] * (1 + Scrap);
    //Weight (perfed.)
    OrderInfo[4] = OrderInfo[2] * (1 - OA2);
    //Weight (perfed. w/scrap)
    OrderInfo[5] = OrderInfo[4] * (1 + Scrap);
    //Number of holes
    OrderInfo[6] = HPSI * Strip * OrderInfo[0]/FoottoInch;
    //Welding time (min)
    OrderInfo[7] = OrderInfo[0]/ WS * 12 * 6/5;

    //Cutoff Time (min)
    OrderInfo[8] = (OD/CutSpd*2*6/5)/Length;
    //Total Time (hours)
    OrderInfo[9] = (OrderInfo[7] + OrderInfo[8])/60;

    //Per tube inch column
    var Costs = [];
    //Crating
    Costs[0] = Crating2/Quantity/Length;
    //Tag
    Costs[1] = TagCost/Length;
    //Shop supplies
    Costs[2] = Shop / FoottoInch;
    //Electric cost
    Costs[3] = OrderInfo[7] * Electric;
    //Blade cost
    Costs[4] = Blade/TPB/Length;
    //Stamping cost
    Costs[5] = Stamping*OrderInfo[1];
    //Material Cost
    Costs[6] = MatCost*OrderInfo[1];
    //Gas Cost
    Costs[7] = Gas*OrderInfo[7]/60;

    //Per tube inch column
    var Costs2 = [];

    //Steel Cost
    var Multiplier = 1.03;
    Costs2[0] = Costs[5] + Costs[6]*Multiplier;
    //Cutoff Labor Cost
    Costs2[1] = OrderInfo[8]*Labor/60;
    //Welding Labor cost
    Costs2[2] = Men*Labor*OrderInfo[7]/60;
    //Supply Cost
    Costs2[3] = Costs[0] + Costs[1] + Costs[2] + Costs[3] + Costs[4] + Costs[7];

    var Costs3 = [];
    //Marginal Cost
    Costs3[0] = Costs2.reduce ((a, b) => a+b, 0) + Setup/(Length * Quantity);
    //Overhead
    //Costs3[1] = OrderInfo[3]/Basis*Overhead;
    //Markup
    Costs3[1] = Costs3[0]*Markup;
    var TotalCost = Costs3[0] + Costs3[1] + B68;

    this.ID = ID;
    this.OD = OD;
    this.Costs = Costs;
    this.Costs2 = Costs2;
    this.Costs3 = Costs3;
    this.OrderInfo = OrderInfo;
    this.TotalCost = TotalCost;
    this.Length = Length;
    this.Angle = Angle;
    this.Quantity = Quantity;
    this.Unit = Unit;
    this.Crate = Crate;
    this.Crating2 = Crating2;
    this.OA2 = OA2;
    }
}

class DrainageInner {
    constructor (Quantity, Material, Gage, Thickness, Pattern,
    Holes, Centers, Diameter, OA, is_OD,
    StripB, StripC, Length, Setup, Crating, TPC, Crate,
    Shop, Electric, Men, CutSpd, TagCost,
    Density, Scrap, Stamping, MatCost, Gas,
    Labor, TPB, Blade, WS, Markup,
    Overhead, Basis, Unit) {

    //Additional Cells
    var B68 = 0; 

    // Unit indicator: 0 - inch, 1 - foot
    const FoottoInch = 12;
    if (Unit) {
        Unit = 12;
    } else {
        Unit = 1;
    }
    var OA2 = 0;
    if (Centers > 0) {
        OA2 = (Holes*Holes)/(Centers*Centers) * OA/100;
    }
    var ID = Diameter;
    var OD = Diameter + 2*Thickness;

    var Crating2 = Crating * Crate;
    var Angle = 90 - Math.acos (StripB/Math.PI/OD) * 180/Math.PI;
    var HPSI = 0;
    if (Holes > 0) {
        HPSI = OA2 * (100/(78.54*Holes*Holes));
    }

    //Per tube inch column
    var OrderInfo = [];
    //Linear ft. material
    OrderInfo[0] = Math.PI * OD / StripC / FoottoInch;
    //Linear ft. material (w/ scrap)
    OrderInfo[1] = OrderInfo[0] * (1 + Scrap);
    //Weight (not perfed.)
    OrderInfo[2] = Thickness * StripB * Density * OrderInfo[0] * FoottoInch;
    //Weight (not perfed. w/scrap)
    OrderInfo[3] = OrderInfo[2] * (1 + Scrap);
    //Weight (perfed.)
    OrderInfo[4] = OrderInfo[2] * (1 - OA2);
    //Weight (perfed. w/scrap)
    OrderInfo[5] = OrderInfo[4] * (1 + Scrap);
    //Number of holes
    OrderInfo[6] = HPSI * StripB * OrderInfo[0]/FoottoInch;
    //Welding time (min)
    OrderInfo[7] = OrderInfo[0]/ WS * 12 * 6/5;
    //Cutoff Time (min)
    OrderInfo[8] = (OD/CutSpd*2*6/5)/Length;
    //Total Time (hours)
    OrderInfo[9] = (OrderInfo[7] + OrderInfo[8])/60;

    //Per tube inch column
    var Costs = [];
    //Crating
    Costs[0] = Crating2/Quantity/Length;
    //Tag
    Costs[1] = TagCost/Length;
    //Shop supplies
    Costs[2] = Shop / FoottoInch;
    //Electric cost
    Costs[3] = OrderInfo[7] * Electric;
    //Blade cost
    Costs[4] = Blade/TPB/Length;
    //Stamping cost
    Costs[5] = Stamping*OrderInfo[1];
    //Material Cost
    Costs[6] = MatCost*OrderInfo[1];
    //Gas Cost
    Costs[7] = Gas*OrderInfo[7]/60;

    //Per tube inch column
    var Costs2 = [];

    //Steel Cost
    var Multiplier = 1.064;
    Costs2[0] = Costs[5] + Costs[6]*Multiplier;
    //Cutoff Labor Cost
    Costs2[1] = OrderInfo[8]*Labor/60;
    //Welding Labor cost
    Costs2[2] = Men*Labor*OrderInfo[7]/60;
    //Supply Cost
    Costs2[3] = Costs[0] + Costs[1] + Costs[2] + Costs[3] + Costs[4] + Costs[7];

    var Costs3 = [];
    //Marginal Cost
    Costs3[0] = Costs2.reduce ((a, b) => a+b, 0) + Setup/(Length * Quantity);
    //Overhead
    //Costs3[1] = OrderInfo[3]/Basis*Overhead;
    //Markup
    Costs3[1] = Costs3[0]*Markup;
    var TotalCost = Costs3[0] + Costs3[1] + B68;

    this.ID = ID;
    this.OD = OD;
    this.Costs = Costs;
    this.Costs2 = Costs2;
    this.Costs3 = Costs3;
    this.OrderInfo = OrderInfo;
    this.TotalCost = TotalCost;
    this.Length = Length;
    this.Angle = Angle;
    this.Quantity = Quantity;
    this.Unit = Unit;
    this.Crate = Crate;
    this.Crating2 = Crating2;
    this.OA2 = OA2;
    }
}

class FilterSeam {
    constructor (Quantity, Material, Gage, Thickness, Pattern,
            Holes, Centers, Diameter, OA, is_OD,
            Strip, Length, Setup, Crating, TPC,
            Shop, Electric, Men, CutSpd, TagCost,
            Density, Scrap, Stamping, MatCost, Gas,
            Labor, TPB, Blade, WS, Markup,
            Overhead, Basis, F14, F22, Unit) {

    //Additional Cells
    var B68 = 0; 

    // Unit indicator: 0 - inch, 1 - foot
    const FoottoInch = 12;
    if (Unit) {
        Unit = 12;
    } else {
        Unit = 1;
    }
    var OA2 = 0;
    if (Centers > 0) {
        OA2 = (Holes*Holes)/(Centers*Centers) * OA/100;
    }
    var ID = Diameter;
    var OD = Diameter + 2*Thickness;
    var Crate = Math.ceil (Quantity/TPC);
    if (TPC == 0) {
        Crate = 0;
    }
    var Crating2 = Crating * Crate;
    var Angle = 90 - Math.acos (Strip/Math.PI/OD) * 180/Math.PI;
    var HPSI = 0;
    if (Holes > 0) {
        HPSI = OA2 * (100/(78.54*Holes*Holes));
    }

    //Per tube inch column
    var OrderInfo = [];
    //Linear ft. material
    OrderInfo[0] = Math.PI * OD / Strip / FoottoInch;
    //Linear ft. material (w/ scrap)
    OrderInfo[1] = OrderInfo[0] * (1 + Scrap);
    //Weight (not perfed.)
    OrderInfo[2] = Thickness * Strip * Density * OrderInfo[0] * FoottoInch;
    //Weight (not perfed. w/scrap)
    OrderInfo[3] = OrderInfo[2] * (1 + Scrap);
    //Weight (perfed.)
    OrderInfo[4] = OrderInfo[2] * (1 - OA2);
    //Weight (perfed. w/scrap)
    OrderInfo[5] = OrderInfo[4] * (1 + Scrap);
    //Number of holes
    OrderInfo[6] = HPSI * Strip * OrderInfo[0]/FoottoInch;
    //Welding time (min)
    OrderInfo[7] = OrderInfo[0]/ WS * 12 * 6/5;
    //Cutoff Time (min)
    OrderInfo[8] = (OD/CutSpd*2*6/5)/Length;
    //Total Time (hours)
    OrderInfo[9] = (OrderInfo[7] + OrderInfo[8])/60;

    //Per tube inch column
    var Costs = [];
    //Crating
    Costs[0] = Crating2/Quantity/Length;
    //Tag
    Costs[1] = TagCost/Length;
    //Shop supplies
    Costs[2] = Shop / FoottoInch;
    //Electric cost
    Costs[3] = OrderInfo[7] * Electric;
    //Blade cost
    Costs[4] = Blade/TPB/Length;
    //Stamping cost
    Costs[5] = Stamping*OrderInfo[1];
    //Material Cost
    Costs[6] = MatCost*OrderInfo[1];
    //Gas Cost
    Costs[7] = Gas*OrderInfo[7]/60;

    //Per tube inch column
    var Costs2 = [];

    //Steel Cost
    var Multiplier = 1.03;
    Costs2[0] = Costs[5] + Costs[6]*Multiplier;
    //Cutoff Labor Cost
    Costs2[1] = OrderInfo[8]*Labor/60;
    //Welding Labor cost
    Costs2[2] = Men*Labor*OrderInfo[7]/60;
    //Supply Cost
    Costs2[3] = Costs[0] + Costs[1] + Costs[2] + Costs[3] + Costs[4] + Costs[7];

    var Costs3 = [];
    //Marginal Cost
    Costs3[0] = Costs2.reduce ((a, b) => a+b, 0) + Setup/(Length * Quantity);
    //Overhead
    //Costs3[1] = OrderInfo[3]/Basis*Overhead;
    //Markup
    Costs3[1] = Costs3[0]*Markup;
    var TotalCost = Costs3[0] + Costs3[1] + B68;

    this.ID = ID;
    this.OD = OD;
    this.Costs = Costs;
    this.Costs2 = Costs2;
    this.Costs3 = Costs3;
    this.OrderInfo = OrderInfo;
    this.TotalCost = TotalCost;
    this.Length = Length;
    this.Angle = Angle;
    this.Quantity = Quantity;
    this.Unit = Unit;
    this.Crate = Crate;
    this.Crating2 = Crating2;
    this.OA2 = OA2;
    }
}

class DrainageOuter {
    constructor (Quantity, Material, Gage, Thickness, Pattern,
    Holes, Centers, Diameter, OA, is_OD,
    StripB, StripC, Length, Setup, Crating, TPC, Crate,
    Shop, Electric, Men, CutSpd, TagCost,
    Density, Scrap, Stamping, MatCost, Gas,
    Labor, TPB, Blade, WS, Markup,
    Overhead, Basis, Unit) {

    //Additional Cells
    var B68 = 0; 

    // Unit indicator: 0 - inch, 1 - foot
    const FoottoInch = 12;
    if (Unit) {
        Unit = 12;
    } else {
        Unit = 1;
    }
    var OA2 = 0;
    if (Centers > 0) {
        OA2 = (Holes*Holes)/(Centers*Centers) * OA/100;
    }
    var ID = Diameter;
    var OD = Diameter + 2*Thickness;

    var Crating2 = Crating * Crate;
    var Angle = 90 - Math.acos (StripB/Math.PI/OD) * 180/Math.PI;
    var HPSI = 0;
    if (Holes > 0) {
        HPSI = OA2 * (100/(78.54*Holes*Holes));
    }

    //Per tube inch column
    var OrderInfo = [];
    //Linear ft. material
    OrderInfo[0] = Math.PI * OD / StripC / FoottoInch;
    //Linear ft. material (w/ scrap)
    OrderInfo[1] = OrderInfo[0] * (1 + Scrap);
    //Weight (not perfed.)
    OrderInfo[2] = Thickness * StripB * Density * OrderInfo[0] * FoottoInch;
    //Weight (not perfed. w/scrap)
    OrderInfo[3] = OrderInfo[2] * (1 + Scrap);
    //Weight (perfed.)
    OrderInfo[4] = OrderInfo[2] * (1 - OA2);
    //Weight (perfed. w/scrap)
    OrderInfo[5] = OrderInfo[4] * (1 + Scrap);
    //Number of holes
    OrderInfo[6] = HPSI * StripB * OrderInfo[0]/FoottoInch;
    //Welding time (min)
    OrderInfo[7] = OrderInfo[0]/ WS * 12 * 6/5;
    //Cutoff Time (min)
    OrderInfo[8] = (OD/CutSpd*2*6/5)/Length;
    //Total Time (hours)
    OrderInfo[9] = (OrderInfo[7] + OrderInfo[8])/60;

    //Per tube inch column
    var Costs = [];
    //Crating
    Costs[0] = Crating2/Quantity/Length;
    //Tag
    Costs[1] = TagCost/Length;
    //Shop supplies
    Costs[2] = Shop / FoottoInch;
    //Electric cost
    Costs[3] = OrderInfo[7] * Electric;
    //Blade cost
    Costs[4] = Blade/TPB/Length;
    //Stamping cost
    Costs[5] = Stamping*OrderInfo[1];
    //Material Cost
    Costs[6] = MatCost*OrderInfo[1];
    //Gas Cost
    Costs[7] = Gas*OrderInfo[7]/60;

    //Per tube inch column
    var Costs2 = [];

    //Steel Cost
    var Multiplier = 1.064;
    Costs2[0] = Costs[5] + Costs[6]*Multiplier;
    //Cutoff Labor Cost
    Costs2[1] = OrderInfo[8]*Labor/60;
    //Welding Labor cost
    Costs2[2] = Men*Labor*OrderInfo[7]/60;
    //Supply Cost
    Costs2[3] = Costs[0] + Costs[1] + Costs[2] + Costs[3] + Costs[4] + Costs[7];

    var Costs3 = [];
    //Marginal Cost
    Costs3[0] = Costs2.reduce ((a, b) => a+b, 0) + Setup/(Length * Quantity);
    //Overhead
    //Costs3[1] = OrderInfo[3]/Basis*Overhead;
    //Markup
    Costs3[1] = Costs3[0]*Markup;
    var TotalCost = Costs3[0] + Costs3[1] + B68;

    this.ID = ID;
    this.OD = OD;
    this.Costs = Costs;
    this.Costs2 = Costs2;
    this.Costs3 = Costs3;
    this.OrderInfo = OrderInfo;
    this.TotalCost = TotalCost;
    this.Length = Length;
    this.Angle = Angle;
    this.Quantity = Quantity;
    this.Unit = Unit;
    this.Crate = Crate;
    this.Crating2 = Crating2;
    this.OA2 = OA2;
    }
}

class Outer {
    constructor (Quantity, Material, Gage, ThicknessB, ThicknessC, Pattern,
        Holes, Centers, Diameter, OA, is_OD,
        Strip, Length, Setup, Crating, TPC, Crate,
        Shop, Electric, Men, CutSpd, TagCost,
        Density, Scrap, Stamping, MatCost, Gas,
        Labor, TPB, Blade, WS, Markup,
        Overhead, Basis, Unit) {

    //Additional Cells
    var B68 = 0; 

    // Unit indicator: 0 - inch, 1 - foot
    const FoottoInch = 12;
    if (Unit) {
        Unit = 12;
    } else {
        Unit = 1;
    }
    var OA2 = 0;
    if (Centers > 0) {
        OA2 = (Holes*Holes)/(Centers*Centers) * OA/100;
    }
    var ID = Diameter;
    var OD = Diameter + 2*ThicknessC;
    var Crate = Math.ceil (Quantity/TPC);
    if (TPC == 0) {
        Crate = 0;
    }
    var Crating2 = Quantity*Length*0.75/12;
    var Angle = 90 - Math.acos (Strip/Math.PI/OD) * 180/Math.PI;
    var HPSI = 0;
    if (Holes > 0) {
        HPSI = OA2 * (100/(78.54*Holes*Holes));
    }
    
    //Per tube inch column
    var OrderInfo = [];
    //Linear ft. material
    OrderInfo[0] = Math.PI * OD / Strip / FoottoInch;
    //Linear ft. material (w/ scrap)
    OrderInfo[1] = OrderInfo[0] * (1 + Scrap);
    //Weight (not perfed.)
    OrderInfo[2] = ThicknessB * Strip * Density * OrderInfo[0] * FoottoInch;
    //Weight (not perfed. w/scrap)
    OrderInfo[3] = OrderInfo[2] * (1 + Scrap);
    //Weight (perfed.)
    OrderInfo[4] = OrderInfo[2] * (1 - OA2);
    //Weight (perfed. w/scrap)
    OrderInfo[5] = OrderInfo[4] * (1 + Scrap);
    //Number of holes
    OrderInfo[6] = HPSI * Strip * OrderInfo[0]/FoottoInch;
    //Welding time (min)
    OrderInfo[7] = OrderInfo[0]/ WS * 12 * 6/5;
    //Cutoff Time (min)
    OrderInfo[8] = (OD/CutSpd*2 + 1)/Length;
    //Total Time (hours)
    OrderInfo[9] = (OrderInfo[7] + OrderInfo[8])/60;

    //Per tube inch column
    var Costs = [];
    //Crating
    Costs[0] = Crating2/Quantity/Length;
    //Tag
    Costs[1] = TagCost/Length;
    //Shop supplies
    Costs[2] = Shop / FoottoInch;
    //Electric cost
    Costs[3] = OrderInfo[7] * Electric;
    //Blade cost
    Costs[4] = Blade/TPB/Length;
    //Stamping cost
    Costs[5] = Stamping*OrderInfo[1];
    //Material Cost
    Costs[6] = MatCost*OrderInfo[3];
    //Gas Cost
    Costs[7] = Gas*OrderInfo[7]/60;

    //Per tube inch column
    var Costs2 = [];

    //Steel Cost
    Costs2[0] = Costs[1] + Costs[2] + Costs[3] + Costs[4] + Costs[5] + Costs[6] + Costs[7];
    //Cutoff Labor Cost
    Costs2[1] = (OrderInfo[8]*Length*(3/4))/144;
    //Welding Labor cost
    Costs2[2] = Men*Labor*OrderInfo[7]/60;
    //Supply Cost
    Costs2[3] = Costs[0] + Costs[1] + Costs[2] + Costs[3] + Costs[4] + Costs[7];

    var Costs3 = [];
    //Marginal Cost
    Costs3[0] = Costs2.reduce ((a, b) => a+b, 0) + Setup/(Length * Quantity);
    //Overhead
    Costs3[1] = OrderInfo[3]/Basis*Overhead;
    //Markups
    Costs3[2] = (Costs3[0] + Costs3[1])*Markup;
    //var TotalCost = Costs3.reduce ((a, b) => a+b, 0) + B68;
    var TotalCost = Costs3[0] + Costs3[1] + Costs3[2]+ B68;
    this.CratingCost = Crating2;
    this.ID = ID;
    this.OD = OD;
    this.Costs = Costs;
    this.Costs2 = Costs2;
    this.Costs3 = Costs3;
    this.OrderInfo = OrderInfo;
    this.TotalCost = TotalCost;
    this.Length = Length;
    this.Angle = Angle;
    this.Quantity = Quantity;
    this.Unit = Unit;
    this.Crate = Crate;
    this.Crating2 = Crating2;
    this.OA2 = OA2;
    }
}

class Prices {
    constructor (TotalPrice, WVad, WVadShipping, Tube, Order) {
        this.TotalPrice = TotalPrice;
        this.WVad = WVad;
        this.WVadShipping = WVadShipping;
        this.Tube = Tube;
        this.Order = Order;
    }
}

class MaterialRequired {
    constructor (Type, Width, LNFT, Grade) {
        this.Type = Type;
        this.Width = Width;
        this.LNFT = LNFT;
        this.R100FT = LNFT/100;
        this.Grade = Grade;
    }
}

class ShroudRawMaterial {
    constructor (Type, Width, LBS, TubeOD) {
        this.Type = Type;
        this.Width = Width;
        this.LBS = LBS;
        this.TubeOD = TubeOD;
    }
}
//End of Classes

function GetStripWidthB (TypeAssembly, OuterMeshWidth) {
    var Rtn;
    switch (TypeAssembly) {
        case "SDL":
            Rtn = OuterMeshWidth;
        break;
        case "SSL":
            Rtn = OuterMeshWidth;
        break;
        case "ECDL":
            Rtn = OuterMeshWidth;
        break;

        case "SLND":
            
        break;

        case "ECND":

        break;

        case "SSWDL":
            Rtn = OuterMeshWidth;
        break;

        case "SSW":

        break;

        case "ECSL":
            Rtn = OuterMeshWidth;
        break;

    }
    return Rtn;
}

function GetDIThickness (TypeAssembly, MasterM8) {
    var Rtn;
    switch (TypeAssembly) {
        case "SDL":
            Rtn = MasterM8;
        break;
        case "SSL":
            Rtn = 0;
        break;
        case "ECDL":
            Rtn = MasterM8;
        break;

        case "SLND":
            Rtn = 0;
        break;

        case "ECND":
            Rtn = 0;
        break;

        case "SSWDL":
            Rtn = MasterM8;
        break;

        case "SSW":
            Rtn = 0;
        break;

        case "ECSL":
            Rtn = 0;
        break;

    }
    return Rtn;
}

function GetMasterD48 (MasterD45) {
    var Rtn;
    switch (MasterD45) {
        case "16x120":
            Rtn = 0.04;
        break;

        case "12x64":
            Rtn = 0.05;
        break;

        case "14x88":
            Rtn = 0.04;
        break;

        case "30x150":
            Rtn = 0.02;
        break;

        case "24x110":
            Rtn = 0.028;
        break;

        case "30x250":
            Rtn = 0.03;
        break;

        case "60x40":
            Rtn = 0.028;
        break;

        case "60x50":
            Rtn = 0.02;
        break;
        
        case "60TW":
            Rtn = 0.022;
        break;

        case "18x210":
            
        break;

        case "136RTD":
            Rtn = 0.039;
        break;
    }
    return Rtn;
}

function GetMasterD49 (DrainageOuterMesh) {
    var Rtn;
    switch (DrainageOuterMesh) {
        case 20:
            Rtn = 0.03;
        break;

        case 10:
            Rtn = 0.045;
        break;

        case 16:
            Rtn = 0.052;
        break;

        case 30:
            Rtn = 0.02;
        break;
    }
    return Rtn;
}
function GetMasterD50 (DrainageOuterMesh) {
    var Rtn;
    switch (DrainageOuterMesh) {
        case 20:
            Rtn = 0.03;
        break;

        case 10:
            Rtn = 0.045;
        break;

        case 16:
            Rtn = 0.052;
        break;

        case 30:
            Rtn = 0.02;
        break;
    }
    return Rtn;
}

function GetMasterD45 (Micron) {
    var Rtn;
    switch (Micron) {
        case 200:
            Rtn = "16x120";
        break;

        case 300:
            Rtn = "12x64";
        break;

        case 250:
            Rtn = "14x88";
        break;

        case 120:
            Rtn = "30x150";
        break;

        case 150:
            Rtn = "24x110";
        break;

        case 115:
            Rtn = "30x250";
        break;

        case 258:
            Rtn = "60x40";
        break;

        case 289:
            Rtn = "60x50";
        break;
        
        case 165:
            Rtn = "60TW";
        break;

        case 197:
            Rtn = "18x210";
        break;

        case "RT300":
            Rtn = "136RTD";
        break;
    }
    return Rtn;
}

function GetInformationE18 (B18, C18) {
    var Rtn;
    if (B18 > 0) {
        Rtn = B18;
    }
    if (B18 == 0) {
        Rtn = C18;
    }
    return Rtn;
}
function GetInformationE19 (B18, D18) {
    var Rtn;
    if (B18 > 0) {
        Rtn = B18;
    }
    if (B18 == 0) {
        Rtn = D18;
    }
    return Rtn;
}

function GetDIMatCost(TypeAssembly, MasterM9, MasterM12) {
    var Rtn;
    switch (TypeAssembly) {
        case "SDL":
            Rtn = MasterM9/12*MasterM12;
        break;
        case "SSL":
            Rtn = 0;
        break;
        case "ECDL":
            Rtn = MasterM9/12*MasterM12;
        break;

        case "SLND":
            Rtn = 0;
        break;

        case "ECND":
            Rtn = 0;
        break;

        case "SSWDL":
            Rtn = MasterM9/12*MasterM12;
        break;

        case "SSW":
            Rtn = 0;
        break;

        case "ECSL":
            Rtn = 0;
        break;
    }
    return Rtn;
}

function GetFIThickness (TypeAssembly, MasterD48) {
    var Rtn;
    switch (TypeAssembly) {
        case "SDL":
            Rtn = MasterD48;
        break;
        case "SSL":
            Rtn = MasterD48;
        break;
        case "ECDL":
            Rtn = MasterD48;
        break;

        case "SLND":
            Rtn = MasterD48;
        break;

        case "ECND":
            Rtn = MasterD48;
        break;

        case "SSWDL":
            Rtn = MasterD48*2;
        break;

        case "SSW":
            Rtn = MasterD48*2;
        break;

        case "ECSL":
            Rtn = 0;
        break;

    }
    return Rtn;    
}

function GetOuterDrainageWidth (TypeAssembly, FilterMeshWidth, OuterMeshWidth) {
    var Rtn;
    //K22 = FilterMeshWidth
    //D25 = OuterMeshWidth
    switch (TypeAssembly) {
        case "SDL":
            Rtn = FilterMeshWidth;
        break;
        case "SSL":
            Rtn = FilterMeshWidth;
        break;
        case "ECDL":
            Rtn = OuterMeshWidth;
        break;

        case "SLND":
            
        break;

        case "ECND":
            
        break;

        case "SSWDL":
            Rtn = OuterMeshWidth;
        break;

        case "SSW":
            
        break;

        case "ECSL":
            Rtn = OuterMeshWidth;
        break;

    }
    return Rtn;
}

function GetFOMatCost (TypeAssembly, MasterG9, MasterG12) {
    var Rtn;
    switch (TypeAssembly) {
        case "SDL":
            Rtn = MasterG9*MasterG12/12;
        break;
        case "SSL":
            Rtn = MasterG9*MasterG12/12;
        break;
        case "ECDL":
            Rtn = 0;
        break;

        case "SLND":
            Rtn = MasterG9*MasterG12/12;
        break;

        case "ECND":
            Rtn = 0;
        break;

        case "SSWDL":
            Rtn = MasterG9*MasterG12/12;
        break;

        case "SSW":
            Rtn = MasterG9*MasterG12/12;
        break;

        case "ECSL":
            Rtn = 0;
        break;
    }
    return Rtn;    
}

function GetFOMatThickness (TypeAssembly, MasterD48) {
    var Rtn;
    switch (TypeAssembly) {
        case "SDL":
            Rtn = MasterD48;
        break;
        case "SSL":
            Rtn = MasterD48;
        break;
        case "ECDL":
            Rtn = 0;
        break;

        case "SLND":
            Rtn = MasterD48;
        break;

        case "ECND":
            Rtn = 0;
        break;

        case "SSWDL":
            Rtn = MasterD48*2;
        break;

        case "SSW":
            Rtn = MasterD48*2;
        break;

        case "ECSL":
            Rtn = 0;
        break;
    }
    return Rtn;
}

function GetFSMatThickness (TypeAssembly, MasterE8) {
    var Rtn;
    switch (TypeAssembly) {
        case "SDL":
            Rtn = 0;
        break;
        case "SSL":
            Rtn = 0;
        break;
        case "ECDL":
            Rtn = MasterE8;
        break;

        case "SLND":
            Rtn = 0;
        break;

        case "ECND":
            Rtn = MasterE8;
        break;

        case "SSWDL":
            Rtn = 0;
            // I change the value to zero 
        break;

        case "SSW":
            Rtn = 0;
        break;

        case "ECSL":
            Rtn = MasterE8;
        break;
    }
    return Rtn;
}

function GetFSMatCost (TypeAssembly, F23) {
    var Rtn;
    switch (TypeAssembly) {
        case "SDL":
            Rtn = 0;
        break;
        case "SSL":
            Rtn = 0;
        break;
        case "ECDL":
            Rtn = F23;
        break;

        case "SLND":
            Rtn = 0;
        break;

        case "ECND":
            Rtn = 0;
        break;

        case "SSWDL":
            Rtn = 0;
        break;

        case "SSW":
            Rtn = 0;
        break;

        case "ECSL":
            Rtn = F23;
        break;
    }
    return Rtn;
}

function GetDOMatThickness (TypeAssembly, MasterM8, MasterI8) {
    var Rtn;
    switch (TypeAssembly) {
        case "SDL":
            Rtn = MasterM8;
        break;
        case "SSL":
            Rtn = MasterM8;
        break;
        case "ECDL":
            Rtn = MasterI8;
        break;

        case "SLND":
            Rtn = 0;
        break;
// i changed value to zero 
        case "ECND":
            Rtn = 0;            
        break;

        case "SSWDL":
            Rtn = MasterM8;
        break;

        case "SSW":
            Rtn = MasterM8;
        break;

        case "ECSL":
            Rtn = MasterI8;
        break;
    }
    return Rtn;
}

function GetDOMatCost (TypeAssembly, MasterI9, MasterI12, MasterI13) {
    var Rtn;
    switch (TypeAssembly) {
        case "SDL":
            Rtn = MasterI9*MasterI12/12;
        break;
        case "SSL":
            Rtn = MasterI9*MasterI12/12;
        break;
        case "ECDL":
            Rtn = MasterI9*MasterI12/12;
        break;

        case "SLND":
            Rtn = 0;
        break;

        case "ECND":
            Rtn = 0;
        break;

        case "SSWDL":
            Rtn = MasterI9*MasterI12/12;
        break;

        case "SSW":
            Rtn = MasterI9*MasterI12/12;
        break;

        case "ECSL":
            Rtn = MasterI9*MasterI12/12;
        break;
    }
    return Rtn;
}

function ReturnInner (Commission, ShroudMatCostBoth, ShroudMatCostIn, AmountOrder,
                        ShroudTypeBoth, Gage, MatThickness, InnerID, StripWidth,
                        Length, SetUpFee) {
    var NewLength = Length+8;
    var Markup = Commission+0.02;
    var MatCost;
    if (ShroudMatCostBoth > 0) {
        MatCost = ShroudMatCostBoth;
    } else {
        if (ShroudMatCostBoth == 0) {
            MatCost = ShroudMatCostIn;
        }
    }
    var Obj =  new Inner (AmountOrder, ShroudTypeBoth, Gage, Gage, MatThickness, "Vector",
                                0, 0, InnerID, 0, 0,
                                StripWidth, NewLength, SetUpFee, 0, 0,
                                0.3, 0.03, 3.5, 4, 0,
                                0.29, 0.08, 0.47, MatCost, 3.1,
                                25, 30, 60, 40, Markup,
                                576000, 1250000, 0);
    return Obj;
}

function ReturnFilterInner (AmountOrder, FilterMashMat, Thickness,
                            Diameter, StripWidth, Length, MatCost) {
    var NewLength = Length + 10;
    var Obj = new FilterInner (AmountOrder, FilterMashMat, 0, Thickness,
            "Vector", 0, 0, Diameter, 0, -1, StripWidth, NewLength, 0, 0,
            30, 0, 0, 0, 3.5, 8, 0, 0,
            0.12, 0, MatCost, 0, 0, 30, 0,
            40, 0.1, 0, 0, 1);
    return Obj;
}

function ReturnFilterOuter (AmountOrder, FilterMeshMat, Thickness,
                            Diameter, StripWidth, Length, MatCost) {
    var NewLength = Length + 10;
    var Obj = new FilterOuter (AmountOrder, FilterMeshMat, 0,
                Thickness, "Vector", 0, 0, Diameter, 0, -1, StripWidth,
                NewLength, 0, 0, 30, 0, 0, 3.5, 8, 0,
                0, 0.12, 0, MatCost, 0, 0, 30, 0,
                40, 0.1, 0, 0, 1);
    return Obj;
}

function ReturnDrainageInner (AmountOrder, FilterMeshMat, Thickness,
                                InnerOD, StripWidthB, StripWidthC, Length, MatCost) {
    
        var NewLength = Length + 10;
        var Obj = new DrainageInner (AmountOrder, FilterMeshMat, 16, Thickness, "Vector",
                0, 0, InnerOD, 0, -1, StripWidthB, StripWidthC, NewLength, 0, 0, 0, 0, 0, 0, 0,
                1, 0, 0, 0.12, 0, MatCost, 0, 0, 30, 0, 40, 0.1,
                0, 0, 1);
        return Obj;
}

function ReturnFilterSeam (AmountOrder, Thickness, Diameter,
                            StripWidth, Length, MatCost, F14, F22) {
    var NewLength = Length + 10;
    var Obj = new FilterSeam (AmountOrder, "304L", 16, Thickness, "Vector", 0, 0,
                Diameter, 0, -1, StripWidth, NewLength, 0, 0,
                30, 0, 0, 3.5, 8, 0, 0, 0.12, 0,
                MatCost, 0, 0, 30, 0, 40, 0.15, 0,
                0, F14, F22, 1);
    return Obj;
}

function ReturnDrainageOuter (AmountOrder, FilterMeshMat, Thickness, Diameter,
                                StripWidthB, StripWidthC, Length, MatCost) {
    var NewLength = Length + 10;
    var Obj = new DrainageOuter (AmountOrder, FilterMeshMat, 16, Thickness, "Vector",
                0, 0, Diameter, 0, -1, StripWidthB, StripWidthC, NewLength, 
                0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0.12, 0, 
                MatCost, 0, 0, 30, 0, 40, 0.1, 0, 0, 1);
    return Obj;
}

function ReturnOuter (AmountOrder, Material, Gage, MatThicknessB,
                        MatThicknessC, Diameter, StripWidth, Length,
                        SetUpCost, MatCost, Commission) {
    var NewLength = Length + 10;
    var Markup = Commission+0.02;
    var Obj = new Outer (AmountOrder, Material, Gage, MatThicknessB, MatThicknessC, "Vector",
                    0, 0, Diameter, 0, -1, StripWidth, NewLength, SetUpCost, 0,
                    0, 0, 0.3, 0.03, 4.5, 4, 0, 0.29, 0.08, 0.47,
                    MatCost, 3.1, 25, 30, 60, 40,
                    Markup, 676000, 1250000, 1);

    return Obj;
}

function GetFullTypeAssembly (TypeAssembly) {
    var Rtn;
    switch (TypeAssembly) {
        case "SDL":
            Rtn = "Standard double drainage layer";
        break;
        case "SSL":
            Rtn = "Standard single drainage layer";
        break;
        case "ECDL":
            Rtn = "Econ double drainage layer";
        break;

        case "SLND":
            Rtn = "Standard no drainage layer";
        break;

        case "ECND":
            Rtn = "Econ no drainage layer";
        break;

        case "SSWDL":
            Rtn = "Standard Square weave drainage layer";
        break;

        case "SSW":
            Rtn = "Strandard Square weave";
        break;

        case "ECSL":
            Rtn = "Econ single drainge layer";
        break;

        case "SDLNI":
            Rtn = "Standard no inner shroud";
        break;

        case "ECSLNI":
            Rtn = "Econ Single Drainage No Inner";
        break;
    }
    return Rtn;
}

function GetInnerDrainageWidth (TypeAssembly, FilterMeshWidth) {
    var Rtn;
    switch (TypeAssembly) {
        case "SDL":
            Rtn = FilterMeshWidth;
        break;
        case "SSL":
            
        break;
        case "ECDL":
            Rtn = FilterMeshWidth;
        break;

        case "SLND":
            
        break;

        case "ECND":
            
        break;

        case "SSWDL":
            
        break;

        case "SSW":
            
        break;

        case "ECSL":
            
        break;
    }
    return Rtn;
}

function GetFilterSeamWidth (TypeAssembly, B22) {
    var Rtn;
    switch (TypeAssembly) {
        case "SDL":
            Rtn = 0;
        break;

        case "SSL":

        break;

        case "ECDL":
            Rtn = B22;
        break;

        case "SLND":

        break;

        case "ECND":
            Rtn = B22;
        break;

        case "SSWDL":

        break;

        case "SSW":

        break;

        case "ECSL":
            Rtn = B22;
        break;
    }
    return Rtn;    
}

function GetLNFTFilterMesh (TypeAssembly, FilterInner, FilterOuter) {
    var Rtn;
    switch (TypeAssembly) {
        case "SDL":
            Rtn = FilterInner+FilterOuter;
        break;

        case "SSL":
            Rtn = FilterInner+FilterOuter;
        break;

        case "ECDL":
            Rtn = FilterOuter;
        break;

        case "SLND":
         Rtn = FilterInner+FilterOuter;
        break;

        case "ECND":
            Rtn = FilterOuter;
        break;

        case "SSWDL":
            Rtn = (FilterInner+FilterOuter)*2;
        break;

        case "SSW":
            Rtn = (FilterInner+FilterOuter)*2;
        break;

        case "ECSL":
            Rtn = FilterOuter;
        break;
    }
    return Rtn;
}

function GetLNFTOuterDrainage (TypeAssembly, B37) {
    var Rtn;
    switch (TypeAssembly) {
        case "SDL":
            Rtn = B37;
        break;

        case "SSL":
            Rtn = B37;
        break;

        case "ECDL":
            Rtn = B37;
        break;

        case "SLND":
         Rtn = 0;
        break;

        case "ECND":
            Rtn = 0;
        break;

        case "SSWDL":
            Rtn = B37;
        break;

        case "SSW":
            Rtn = 0;
        break;

        case "ECSL":
            Rtn = B37;
        break;
    }
    return Rtn;
}

function GetFilterSeamSP (TypeAssembly, B37) {
    var Rtn;
    switch (TypeAssembly) {
        case "SDL":
            Rtn = 0;
        break;

        case "SSL":
            Rtn = 0;
        break;

        case "ECDL":
            Rtn = B37;
        break;

        case "SLND":
         Rtn = 0;
        break;

        case "ECND":
            Rtn = B37;
        break;

        case "SSWDL":
            Rtn = 0;
        break;

        case "SSW":
            Rtn = 0;
        break;

        case "ECSL":
            Rtn = B37;
        break;
    }
    return Rtn;
}

function GetLNFTFilterSeam (TypeAssembly, B39) {
    var Rtn;
    switch (TypeAssembly) {
        case "SDL":
            Rtn = 0;
        break;

        case "SSL":
            Rtn = 0;
        break;

        case "ECDL":
            Rtn = B39;
        break;

        case "SLND":
         Rtn = 0;
        break;

        case "ECND":
            Rtn = B39;
        break;

        case "SSWDL":
            Rtn = 0;
        break;

        case "SSW":
            Rtn = 0;
        break;

        case "ECSL":
            Rtn = B39;
        break;
    }
    return Rtn;
}

function GetLNFTInnerDrainage (TypeAssembly, B38) {
    var Rtn;
    switch (TypeAssembly) {
        case "SDL":
            Rtn = B38;
        break;

        case "SSL":
            Rtn = 0;
        break;

        case "ECDL":
            Rtn = B38;
        break;

        case "SLND":
         Rtn = 0;
        break;

        case "ECND":
            Rtn = 0;
        break;

        case "SSWDL":
            Rtn = 0;
        break;

        case "SSW":
            Rtn = 0;
        break;

        case "ECSL":
            Rtn = 0;
        break;
    }
    return Rtn;    
}

function GetGradeOuterDrainage (TypeAssembly, B19) {
    var Rtn;
    switch (TypeAssembly) {
        case "SDL":
            Rtn = B19;
        break;

        case "SSL":
            Rtn = B19;
        break;

        case "ECDL":
            Rtn = B19;
        break;

        case "SLND":

        break;

        case "ECND":
            
        break;

        case "SSWDL":
            Rtn = B19;
        break;

        case "SSW":
            
        break;

        case "ECSL":
            Rtn = B19;
        break;
    }
    return Rtn;
}

function GetGradeInnerDrainage (TypeAssembly, B19) {
    var Rtn;
    switch (TypeAssembly) {
        case "SDL":
            Rtn = B19;
        break;

        case "SSL":
            
        break;

        case "ECDL":
            Rtn = B19;
        break;

        case "SLND":

        break;

        case "ECND":
            
        break;

        case "SSWDL":
            
        break;

        case "SSW":
            
        break;

        case "ECSL":
            
        break;
    }
    return Rtn;
}

function GetGradeFilterSeam (TypeAssembly, B19) {
    var Rtn;
    switch (TypeAssembly) {
        case "SDL":
            
        break;

        case "SSL":
            
        break;

        case "ECDL":
            Rtn = B19;
        break;

        case "SLND":

        break;

        case "ECND":
            Rtn = B19;
        break;

        case "SSWDL":
            
        break;

        case "SSW":
            
        break;

        case "ECSL":
            Rtn = B19;
        break;
    }
    return Rtn;
}






function Data (TypeAssembly, Commission, AmountOrder, SetUpFee, Length,
                Micron, ValPerOrder, ShippingCharge, ShroudTypeBoth, ShroudTypeOut,
                ShroudTypeIn, ShroudMatCostBoth, ShroudMatCostOut, ShroudMatCostIn,
                FilterMeshMat, FilterMeshCost, WidthFilterSeam,
                DrainageOuterMesh, OuterMeshWidth, DrainageOuterCost, DrainageInnerMesh, DrainageInnerCost,
                InnerID, Gage, MatThickness, StripWidth, OSGage,
                OSMatThickness, OSStripWidth) {

    var data = {};
    var TypeAssembly = TypeAssembly.toUpperCase();

    var FilterMeshWidth = OSStripWidth - 0.2;
    var InformationE19 = GetInformationE19 (ShroudMatCostBoth, ShroudMatCostIn);
    var Inner = ReturnInner (Commission, ShroudMatCostBoth, ShroudMatCostIn, AmountOrder, ShroudTypeBoth, Gage, MatThickness, InnerID, StripWidth, Length, SetUpFee);                             
    console.log (Inner);

    var MasterD45 = GetMasterD45 (Micron);
    var MasterD48 = GetMasterD48 (MasterD45);
    var MasterD49 = GetMasterD49 (DrainageOuterMesh);
    var MasterD50 = GetMasterD50 (DrainageInnerMesh);
    var DIThickness = GetDIThickness(TypeAssembly, MasterD49);
    //console.log(DIThickness);
    var DIOuterDrainageWidth = GetOuterDrainageWidth (TypeAssembly, FilterMeshWidth, OuterMeshWidth);
    var DIStripWidthB = GetStripWidthB(TypeAssembly, DIOuterDrainageWidth, FilterMeshWidth);
    var DIStripWidthC = OSStripWidth;
    var DIMatCost = GetDIMatCost(TypeAssembly, DrainageOuterCost, (OSStripWidth-0.2));
    var DrainageInner = ReturnDrainageInner (AmountOrder, FilterMeshMat, DIThickness, Inner.OD, DIStripWidthB, DIStripWidthC, Length, DIMatCost);
    // console.log ('DI mat cost ' + DIMatCost);

    var FIMatCost = FilterMeshCost*(OSStripWidth - 0.2)/12;
    var FIThickness = GetFIThickness (TypeAssembly, MasterD48);
    var FilterInner = ReturnFilterInner (AmountOrder, FilterMeshMat, FIThickness, DrainageInner.OD, FilterMeshWidth, Length, FIMatCost);
    //console.log (FilterInner);

    var FOMatThickness = GetFOMatThickness (TypeAssembly, MasterD48);
    var FOMatCost = GetFOMatCost(TypeAssembly, FilterMeshCost, FilterMeshWidth);
    var FilterOuter = ReturnFilterOuter (AmountOrder, FilterMeshMat, FOMatThickness, FilterInner.OD, FilterMeshWidth, Length, FOMatCost);
    //console.log ('filter Outer cost material ' + FOMatCost);

    var FSF14 = 12/WidthFilterSeam;
    var FSF23 = FilterMeshCost*WidthFilterSeam/12;
    var FSMatThickness = GetFSMatThickness (TypeAssembly, MasterD48);
    var FSMatCost  = GetFSMatCost (TypeAssembly, FSF23);
    var FilterSeam = ReturnFilterSeam (AmountOrder, FSMatThickness, FilterOuter.OD, FilterMeshWidth, Length, FSMatCost, FSF14, FSF23);
    //console.log (FilterSeam);

    var InformationE25 = 1.55;
    var DOMatThickness = GetDOMatThickness (TypeAssembly, MasterD49, MasterD50);
    var DOStripWidthB = DIStripWidthB;
    var DOStripWidthC = DIStripWidthC;
    var DOOuterDrainageWidth = DIOuterDrainageWidth;
    var DOMatCost = GetDOMatCost (TypeAssembly, DrainageOuterCost, DOOuterDrainageWidth, OuterMeshWidth);
    var DrainageOuter = ReturnDrainageOuter (AmountOrder, FilterMeshMat, DOMatThickness, FilterSeam.OD, DOStripWidthB, DOStripWidthC, Length, DOMatCost);
    //console.log (DrainageOuter);

    var InformationE18 = GetInformationE18 (ShroudMatCostBoth, ShroudMatCostOut);

     //var InformationE19 = GetInformationE19 (ShroudMatCostBoth, ShroudMatCostIn);
    // var Outer = ReturnOuter (AmountOrder, Material, Gage, MatThicknessB,
    //                     MatThicknessC, Diameter, StripWidth, Length,
    //                     SetUpCost, MatCost, Commission);
    var Outer = ReturnOuter (AmountOrder, ShroudTypeBoth, Gage, OSGage, 
                        OSMatThickness, DrainageOuter.OD, OSStripWidth, Length, 
                        SetUpFee, InformationE18, Commission);
    //console.log (Outer);

    var FullTypeAssembly = GetFullTypeAssembly (TypeAssembly);
    var NewLength = Length + 10;


    //console.log("TypeAssembly " + FullTypeAssembly);

    var DPPrice = (Inner.TotalCost + FilterInner.TotalCost*NewLength/Length + 
                        FilterOuter.TotalCost*NewLength/Length + 
                        DrainageOuter.TotalCost*NewLength/Length + 
                        Outer.TotalCost + 
                        DrainageInner.TotalCost*NewLength/Length + 
                        FilterSeam.TotalCost*NewLength/Length)*12;
    var SPPrice = DPPrice + 5;
    var MCPrice = (Inner.Costs3[0] + FilterInner.Costs3[0] + FilterOuter.Costs3[0] +
                            DrainageOuter.Costs3[0] + Outer.Costs3[0] + DrainageInner.Costs3[0] +
                            FilterSeam.Costs3[0])*12;
    
    var InformationI39 = ValPerOrder*(55 + (MCPrice*1.2))/(AmountOrder*Length/12);
    var InformationI40 = ShippingCharge/(AmountOrder*Length/12);
    var SPWVad = SPPrice + InformationI39;
    var SPWVadShipping = SPWVad + InformationI40;
    var SPTube = SPWVadShipping*Length/12;
    var SPOrder = SPTube*AmountOrder;
    var SP = new Prices (SPPrice, SPWVad, SPWVadShipping, SPTube, SPOrder);
   // console.log (InformationI39 + '   ' + InformationI40+ '   ' +MCPrice+ '   ' + DPPrice+ '   ' + NewLength +'    +  '+FullTypeAssembly);
    // console.log (  'kkkk'+AmountOrder, ShroudTypeBoth, Gage, OSGage, 
    //                     OSMatThickness, DrainageOuter.OD, OSStripWidth, Length, 
    //                     SetUpFee, InformationE18, Commission );
    
    
    var DPWVad = DPPrice + InformationI39;
    var DPWVadShipping = DPWVad + InformationI40;
    var DPTube = DPWVadShipping*Length/12;
    var DPOrder = DPTube*AmountOrder;
    var DP = new Prices (DPPrice, DPWVad, DPWVadShipping, DPTube, DPOrder);
    //console.log (DP);

    var MCWVad = MCPrice + InformationI39;
    var MCWVadShipping = MCWVad + InformationI40;
    var MCTube = MCWVadShipping*Length/12;
    var MCOrder = MCTube*AmountOrder;
    var MC = new Prices (MCPrice, MCWVad, MCWVadShipping, MCTube, MCOrder);
    //console.log (MC);

    var SellPrice = (SP.TotalPrice - MC.TotalPrice)*(AmountOrder*Length/12);
    var Discount = (DP.TotalPrice - MC.TotalPrice)*(AmountOrder*Length/12);
    //console.log (SellPrice);
    //console.log (Discount);

    var FilterMeshType = MasterD45;
    var OuterDrainageType = DrainageOuterMesh;
    var InnerDrainageType = DrainageInnerMesh;
    var FilterSeamType = MasterD45;

    FilterMeshWidth;
    var OuterDrainageWidth =  GetOuterDrainageWidth (TypeAssembly, FilterMeshWidth, OuterMeshWidth)
    var InnerDrainageWidth =  GetInnerDrainageWidth (TypeAssembly, FilterMeshWidth);
    var FilterSeamWidth = GetFilterSeamWidth (TypeAssembly, WidthFilterSeam);

    var FilterInnerE38 = FilterInner.Length*FilterInner.Quantity*FilterInner.OrderInfo[1];
    var FilterOuterE38 = FilterOuter.Length*FilterOuter.Quantity*FilterOuter.OrderInfo[1];
    var LNFTFilterMesh = GetLNFTFilterMesh (TypeAssembly, FilterInnerE38, FilterOuterE38);
    var DrainageOuterE38 = DrainageOuter.Length*DrainageOuter.Quantity*DrainageOuter.OrderInfo[1];
    var LNFTOuterDrainage = GetLNFTOuterDrainage (TypeAssembly, DrainageOuterE38);
    var DrainageInnerE38 = DrainageInner.Length*DrainageInner.Quantity*DrainageInner.OrderInfo[1];
    var LNFTInnerDrainage = GetLNFTInnerDrainage (TypeAssembly, DrainageInnerE38);
    var FilterSeamSP = GetFilterSeamSP (TypeAssembly, DrainageOuterE38);
    var LNFTFilterSeam = GetLNFTFilterSeam (TypeAssembly, FilterSeamSP);
    
    var GradeFilterMesh = FilterMeshMat;
    var GradeOuterDrainage = GetGradeOuterDrainage (TypeAssembly, FilterMeshMat);
    var GradeInnerDrainage = GetGradeInnerDrainage (TypeAssembly, FilterMeshMat);
    var GradeFilterSeam = GetGradeFilterSeam (TypeAssembly, FilterMeshMat);
    
    var FilterMeshMR = new MaterialRequired (FilterMeshType, FilterMeshWidth, LNFTFilterMesh, GradeFilterMesh);
    var OuterDrainageMR = new MaterialRequired (OuterDrainageType, OuterDrainageWidth, LNFTOuterDrainage, GradeOuterDrainage);
    var InnerDrainageMR = new MaterialRequired (InnerDrainageType, InnerDrainageWidth, LNFTInnerDrainage, GradeInnerDrainage);
    var FilterSeamMR = new MaterialRequired (FilterSeamType, FilterSeamWidth, LNFTFilterSeam, GradeFilterSeam);
    //console.log (FilterMeshMR);
    //console.log (OuterDrainageMR);
    //console.log (InnerDrainageMR);
    //console.log (FilterSeamMR);
    var TypeSO, TypeSI;
    if (ShroudTypeBoth) {
        TypeSO = ShroudTypeBoth;
        TypeSI = ShroudTypeBoth;
    }else{
        TypeSO = ShroudTypeOut;
        TypeSI = ShroudTypeIn;
    }
       
    ///////////////////
    var WidthSO = OSStripWidth;
    var WidthSI = StripWidth;
    var LBSSO = Outer.Length*Outer.Quantity*Outer.OrderInfo[3];
    var LBSSI = Inner.Length*Inner.Quantity*Inner.OrderInfo[3]
    var TubeSO = Outer.OD;
    var TubeSI = Inner.OD;
    var ShroudOuterSRM = new ShroudRawMaterial (TypeSO, WidthSO, LBSSO, TubeSO);
    var ShroudInnerSRM = new ShroudRawMaterial (TypeSI, WidthSI, LBSSI, TubeSI);
    //console.log (ShroudOuterSRM);
    //console.log (ShroudInnerSRM);

    var InformationF44  = Outer.CratingCost/(Length*AmountOrder/12)
    var InformationE45 = (SPPrice - InformationF44)*0.05;
    var FullPrice = (Length*AmountOrder/12)*InformationE45;
    var FullPrice2 = Outer.CratingCost;
    var InformationE46 = (DPPrice - InformationF44)*0.05;
    var DiscountPrice = (Length*AmountOrder/12)*InformationE46;
    //console.log (FullPrice);
    //console.log (FullPrice2);
    //console.log (DiscountPrice);
    data['ShroudOuterSRM'] = ShroudOuterSRM;
    data['ShroudInnerSRM'] = ShroudInnerSRM;
    data['FilterMeshMR'] = FilterMeshMR;
    data['OuterDrainageMR'] = OuterDrainageMR;
    data['InnerDrainageMR'] = InnerDrainageMR;
    data['FilterSeamMR'] = FilterSeamMR;
    data['SP'] = SP;
    data['DP'] = DP;
    data['MC'] = MC;
    data['DIThickness'] = DIThickness;
    data['DOMatThickness'] = DOMatThickness;
    data['FOMatThickness'] = FOMatThickness;
    data['FSMatThickness'] = FSMatThickness;
    data['FIThickness'] = FIThickness;
    data['DIMatCost'] = DIMatCost;
    data['DOMatCost'] = DOMatCost;
    data['FOMatCost'] = FOMatCost;
    data['FSMatCost'] = FSMatCost;
    data['FIMatCost'] = FIMatCost;
    data['InformationE19'] = InformationE19; //inner mat cost
    data['FullTypeAssembly'] = FullTypeAssembly;

 
    return data;
}

//Instead of percents use fractions