export interface BaseItem {
    content: string
}

export class EntityItem implements BaseItem {
    public type = 'entity'

    public constructor(public entity: string, public id: string, public property: string, public content: string) {
    }
}

export class SimpleItem implements BaseItem {
    public type = 'simple'

    public constructor(public namespace: string, public locale: string, public name: string, public content: string) {
    }
}