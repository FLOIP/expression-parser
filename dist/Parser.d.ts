export interface FilePosition {
    offset: number;
    line: number;
    column: number;
}
export interface FileRange {
    start: FilePosition;
    end: FilePosition;
    source: string;
}
export interface LiteralExpectation {
    type: "literal";
    text: string;
    ignoreCase: boolean;
}
export interface ClassParts extends Array<string | ClassParts> {
}
export interface ClassExpectation {
    type: "class";
    parts: ClassParts;
    inverted: boolean;
    ignoreCase: boolean;
}
export interface AnyExpectation {
    type: "any";
}
export interface EndExpectation {
    type: "end";
}
export interface OtherExpectation {
    type: "other";
    description: string;
}
export type Expectation = LiteralExpectation | ClassExpectation | AnyExpectation | EndExpectation | OtherExpectation;
declare class _PeggySyntaxError extends Error {
    static buildMessage(expected: Expectation[], found: string | null): string;
    message: string;
    expected: Expectation[];
    found: string | null;
    location: FileRange;
    name: string;
    constructor(message: string, expected: Expectation[], found: string | null, location: FileRange);
    format(sources: {
        source?: any;
        text: string;
    }[]): string;
}
export interface TraceEvent {
    type: string;
    rule: string;
    result?: any;
    location: FileRange;
}
export interface ParseOptions {
    filename?: string;
    startRule?: "Start";
    tracer?: any;
    [key: string]: any;
}
export type ParseFunction = <Options extends ParseOptions>(input: string, options?: Options) => Options extends {
    startRule: infer StartRule;
} ? StartRule extends "Start" ? Start : Start : Start;
export declare const parse: ParseFunction;
export declare const PeggySyntaxError: typeof _PeggySyntaxError;
export type PeggySyntaxError = _PeggySyntaxError;
export type Start = (Expression | string)[];
export type Expression = EscapedIdentifier | ClosedExpression | OpenExpression;
export type OpenExpression = MemberAccess | Function_1;
export type ClosedExpression = ExpressionTypes;
export type ExpressionIdentifier = {
    source: string | undefined;
    start: {
        offset: number;
        line: number;
        column: number;
    };
    end: {
        offset: number;
        line: number;
        column: number;
    };
};
export type ExpressionTypes = EscapedIdentifier | Logic | Math_1 | Concatenation | Function_1 | Null | Bool | MemberAccess;
export type Function_1 = {
    type: string;
    call: any;
    args: any;
    chain: any;
    location: any;
};
export type ChainFunction = string;
export type FunctionArgs = FunctionArgTypes;
export type FunctionArgTypes = Concatenation | Logic | Math_1 | FunctionArgInnerFunction | Null | Bool | MemberAccess | QuotedText | string;
export type FunctionArgInnerFunction = Function_1;
export type MemberAccess = {
    type: string;
    key: any;
    location: any;
};
export type Math_1 = {
    type: string;
    lhs: any;
    rhs: any;
    operator: any;
    location: any;
};
export type MathArg = (Math_1 | MathArgTypes) | MathArgTypes;
export type MathArgTypes = Function_1 | MemberAccess | string | QuotedText;
export type Logic = {
    type: string;
    lhs: any;
    rhs: any;
    operator: any;
    location: any;
};
export type LogicArg = (LogicArgTypes | Logic) | LogicArgTypes;
export type LogicArgTypes = Math_1 | Function_1 | Null | Bool | MemberAccess | string | QuotedText;
export type Concatenation = {
    type: string;
    lhs: any;
    rhs: any;
    location: any;
};
export type ConcatenationArg = EscapedIdentifier | Math_1 | Logic | Function_1 | MemberAccess | QuotedText;
export type EscapedIdentifier = {
    type: string;
    location: any;
};
export type Null = {
    type: string;
    location: any;
};
export type Bool = {
    type: string;
    value: any;
    location: any;
};
export type Backslash = "\\";
export type EscapedSingleQuote = "'";
export type EscapedDoubleQuote = '"';
export type QuotedText = SingleQuotedText | DoubleQuotedText;
export type InnerDoubleQuotedText = string;
export type InnerSingleQuotedText = string;
export type SingleQuotedText = InnerSingleQuotedText;
export type DoubleQuotedText = InnerDoubleQuotedText;
export type OpenParen = "(";
export type CloseParen = ")";
export type Identifier = "@";
export type ConcatOperator = "&";
export type ArgDelimiter = [",", Ws[]];
export type Text = string;
export type Chars = string;
export type Ws = string;
export type ValidVariableCharacters = string;
export type LogicChars = "<=" | ">=" | "<>" | string | "!=";
export type MathChars = string;
export type Numbers = string;
export {};
